<?php

namespace App\Http\Controllers;

use App\Support\FarmlinkCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(): View
    {
        return view('cart', [
            'items' => FarmlinkCatalog::cartItems(),
            'totals' => FarmlinkCatalog::cartTotals(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        abort_unless($product = FarmlinkCatalog::find($data['product']), 404);

        $cart = session('cart', []);
        $cart[$product['slug']] = min(($cart[$product['slug']] ?? 0) + $data['quantity'], $product['stock']);
        session(['cart' => $cart]);

        return redirect()
            ->route('cart')
            ->with('status', "{$product['name']} added to your cart.");
    }

    public function update(Request $request, string $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $cart = session('cart', []);

        if ($data['quantity'] === 0) {
            unset($cart[$product]);
        } elseif ($item = FarmlinkCatalog::find($product)) {
            $cart[$product] = min($data['quantity'], $item['stock']);
        }

        session(['cart' => $cart]);

        return redirect()->route('cart')->with('status', 'Cart updated.');
    }

    public function destroy(string $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$product]);
        session(['cart' => $cart]);

        return redirect()->route('cart')->with('status', 'Item removed from cart.');
    }
}
