<?php

namespace App\Http\Controllers;

use App\Support\FarmlinkCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category', 'All Products');

        return view('marketplace', [
            'categories' => FarmlinkCatalog::categories(),
            'category' => $category,
            'products' => FarmlinkCatalog::filtered($category),
        ]);
    }

    public function show(string $product): View
    {
        abort_unless($item = FarmlinkCatalog::find($product), 404);

        return view('products.show', ['product' => $item]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => array_slice(FarmlinkCatalog::categories(), 1),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'farm' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:1'],
            'unit' => ['required', 'string', 'max:20'],
            'category' => ['required', 'string', 'max:80'],
            'stock' => ['required', 'integer', 'min:1'],
            'minimum' => ['required', 'integer', 'min:1'],
            'pickup' => ['required', 'string', 'max:160'],
            'image' => ['nullable', 'url'],
            'description' => ['required', 'string', 'max:500'],
        ]);

        $product = FarmlinkCatalog::addProduct($data);

        return redirect()
            ->route('products.show', $product['slug'])
            ->with('status', 'Product added to the marketplace.');
    }
}
