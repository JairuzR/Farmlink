<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(): View
    {
        $items = auth()->user()
            ->cartItems()
            ->with(['product.primaryImage', 'product.farmer'])
            ->get();

        $total = $items->sum(fn($item) => $item->subtotal());

        return view('cart', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);

        // Don't allow farmers to buy their own products
        if (auth()->user()->id === $product->user_id) {
            return back()->with('error', 'You cannot add your own product to cart.');
        }

        $item = CartItem::firstOrNew([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
        ]);

        // If already in cart, add to existing quantity
        $item->quantity = ($item->exists ? $item->quantity : 0) + $request->quantity;

        // Cap at available stock
        $item->quantity = min($item->quantity, $product->stock);
        $item->save();

        return back()->with('success', "{$product->title} added to cart.");
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->firstOrFail();

        $item->update([
            'quantity' => min($request->quantity, $product->stock),
        ]);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Product $product)
    {
        CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}