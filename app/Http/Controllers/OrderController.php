<?php

namespace App\Http\Controllers;

use App\Support\FarmlinkCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(): RedirectResponse
    {
        $items = FarmlinkCatalog::cartItems();

        if ($items === []) {
            return redirect()->route('cart')->with('status', 'Your cart is empty.');
        }

        $order = [
            'id' => 'FL-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'created_at' => now()->format('F j, Y g:i A'),
            'status' => 'Pending',
            'items' => $items,
            'totals' => FarmlinkCatalog::cartTotals(),
        ];

        session()->push('orders.pending', $order);
        session()->forget('cart');

        return redirect()->route('orders.pending')->with('status', 'Order placed. The farmer will confirm it soon.');
    }

    public function pending(): View
    {
        return view('orders.pending', [
            'orders' => array_reverse(session('orders.pending', [])),
        ]);
    }

    public function delivered(): View
    {
        return view('orders.delivered', [
            'orders' => array_reverse(session('orders.delivered', self::sampleDeliveredOrders())),
        ]);
    }

    public function markDelivered(string $order): RedirectResponse
    {
        $pending = session('orders.pending', []);
        $delivered = session('orders.delivered', self::sampleDeliveredOrders());

        foreach ($pending as $index => $pendingOrder) {
            if ($pendingOrder['id'] === $order) {
                $pendingOrder['status'] = 'Delivered';
                $pendingOrder['delivered_at'] = now()->format('F j, Y');
                $delivered[] = $pendingOrder;
                unset($pending[$index]);
                break;
            }
        }

        session(['orders.pending' => array_values($pending), 'orders.delivered' => $delivered]);

        return redirect()->route('orders.delivered')->with('status', 'Order marked as delivered.');
    }

    private static function sampleDeliveredOrders(): array
    {
        $product = FarmlinkCatalog::find('fresh-tomatoes');

        return [[
            'id' => 'FL-20260418-TOMA',
            'created_at' => 'April 18, 2026',
            'delivered_at' => 'April 18, 2026',
            'status' => 'Delivered',
            'items' => [[
                'product' => $product,
                'quantity' => 2,
                'line_total' => 240,
            ]],
            'totals' => [
                'subtotal' => 240,
                'delivery' => 50,
                'total' => 290,
            ],
        ]];
    }
}
