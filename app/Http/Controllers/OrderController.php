<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(Request $request): RedirectResponse
    {
        $request->validate([
            'delivery_address' => ['required', 'string'],
            'payment_method'   => ['required', 'in:cod,gcash,maya'],
        ]);

        $user  = auth()->user();
        $items = $user->cartItems()->with('product')->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        // One order per farmer
        foreach ($items->groupBy(fn($i) => $i->product->user_id) as $farmerId => $farmerItems) {
            $subtotal     = $farmerItems->sum(fn($i) => $i->product->price * $i->quantity);
            $deliveryFee  = 0; // flat 0 for now — hook delivery fee logic here later
            $total        = $subtotal + $deliveryFee;
            $platformCut  = round($total * 0.05, 2);
            $farmerPayout = round($total - $platformCut, 2);

            $order = Order::create([
                'buyer_id'         => $user->id,
                'farmer_id'        => $farmerId,
                'status'           => 'pending',
                'subtotal'         => $subtotal,
                'delivery_fee'     => $deliveryFee,
                'total'            => $total,
                'delivery_address' => $request->delivery_address,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'unpaid',
            ]);

            foreach ($farmerItems as $item) {
                $order->items()->create([
                    'product_id'    => $item->product_id,
                    'quantity'      => $item->quantity,
                    'price_at_time' => $item->product->price,
                ]);
            }

            \App\Models\Transaction::create([
                'order_id'      => $order->id,
                'amount'        => $total,
                'platform_cut'  => $platformCut,
                'farmer_payout' => $farmerPayout,
                'method'        => $request->payment_method,
                'status'        => 'pending',
            ]);
        }

        $user->cartItems()->delete();

        return redirect()->route('orders.pending')
            ->with('success', 'Order placed! The farmer will confirm it soon.');
    }

    public function pending(): View
    {
        $orders = auth()->user()
            ->buyerOrders()
            ->with(['items.product.primaryImage', 'items.product.farmer'])
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->latest()
            ->get();

        return view('orders.pending', compact('orders'));
    }

    public function delivered(): View
    {
        $orders = auth()->user()
            ->buyerOrders()
            ->with(['items.product.primaryImage', 'items.product.farmer'])
            ->where('status', 'delivered')
            ->latest()
            ->get();

        return view('orders.delivered', compact('orders'));
    }

    public function markDelivered(Order $order): RedirectResponse
    {
        abort_unless($order->buyer_id === auth()->id(), 403);

        $order->update(['status' => 'delivered']);

        return redirect()->route('orders.delivered')
            ->with('success', 'Order marked as delivered.');
    }

    public function incoming(): View
    {
        $orders = auth()->user()
            ->farmerOrders()
            ->with(['items.product.primaryImage', 'buyer'])
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->latest()
            ->get();

        return view('orders.incoming', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->farmer_id === auth()->id(), 403);

        $request->validate([
            'status' => ['required', 'in:confirmed,preparing,out_for_delivery,delivered,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        // If delivered + was GCash/Maya, mark as paid
        if ($request->status === 'delivered' && in_array($order->payment_method, ['gcash', 'maya'])) {
            $order->update(['payment_status' => 'paid']);
            $order->transaction?->update(['status' => 'completed']);
        }

        // COD: mark paid when delivered
        if ($request->status === 'delivered' && $order->payment_method === 'cod') {
            $order->update(['payment_status' => 'paid']);
            $order->transaction?->update(['status' => 'completed']);
        }

        return back()->with('success', 'Order status updated.');
    }
}