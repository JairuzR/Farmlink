<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    // Farmer: show create form
    public function create(): View
    {
        return view('products.create', [
            'categories' => Category::orderBy('name')->get(),
            'globalTags' => Tag::whereNull('user_id')->orderBy('name')->get(),
            'myTags'     => Tag::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }

    // Farmer: save new product
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:120'],
            'description'      => ['required', 'string', 'max:2000'],
            'price'            => ['required', 'numeric', 'min:0.01'],
            'unit'             => ['required', 'string', 'max:30'],
            'stock'            => ['required', 'integer', 'min:0'],
            'minimum_order'    => ['required', 'integer', 'min:1'],
            'pickup_location'  => ['nullable', 'string', 'max:200'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'harvest_date'     => ['nullable', 'date'],
            'is_available'     => ['boolean'],
            'tags'             => ['nullable', 'array'],
            'tags.*'           => ['exists:tags,id'],
            'new_tags'         => ['nullable', 'string', 'max:300'], // comma-separated custom tag names
            'images'           => ['nullable', 'array', 'max:5'],
            'images.*'         => ['image', 'max:3072'], // 3MB per image
        ]);

        $product = Product::create([
            'user_id'         => auth()->id(),
            'category_id'     => $data['category_id'] ?? null,
            'title'           => $data['title'],
            'description'     => $data['description'],
            'price'           => $data['price'],
            'unit'            => $data['unit'],
            'stock'           => $data['stock'],
            'minimum_order'   => $data['minimum_order'],
            'pickup_location' => $data['pickup_location'] ?? null,
            'harvest_date'    => $data['harvest_date'] ?? null,
            'is_available'    => $request->boolean('is_available', true),
            'status'          => $data['stock'] > 10 ? 'in_stock' : ($data['stock'] > 0 ? 'low_stock' : 'out_of_stock'),
        ]);

        // Attach existing tags
        $tagIds = $data['tags'] ?? [];

        // Create & attach custom tags (farmer-specific)
        if (!empty($data['new_tags'])) {
            $names = array_filter(array_map('trim', explode(',', $data['new_tags'])));
            foreach ($names as $name) {
                $tag = Tag::firstOrCreate(
                    ['name' => $name, 'user_id' => auth()->id()],
                    ['slug' => \Illuminate\Support\Str::slug($name) . '-' . auth()->id()]
                );
                $tagIds[] = $tag->id;
            }
        }

        $product->tags()->sync($tagIds);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $isPrimary = true;
            foreach ($request->file('images') as $image) {
                $path = $image->store('products/' . $product->id, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'is_primary' => $isPrimary,
                ]);
                $isPrimary = false;
            }
        }

        return redirect()
            ->route('products.show', $product->slug)
            ->with('status', 'Product listed successfully!');
    }

    // Public: show single product
    public function show(Product $product): View
    {
        $product->load(['farmer.socialLinks', 'category', 'tags', 'images', 'reviews.user']);

        return view('products.show', compact('product'));
    }

    // Farmer: edit form
    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('products.edit', [
            'product'    => $product->load('tags', 'images'),
            'categories' => Category::orderBy('name')->get(),
            'globalTags' => Tag::whereNull('user_id')->orderBy('name')->get(),
            'myTags'     => Tag::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }

    // Farmer: update product
    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'title'           => ['required', 'string', 'max:120'],
            'description'     => ['required', 'string', 'max:2000'],
            'price'           => ['required', 'numeric', 'min:0.01'],
            'unit'            => ['required', 'string', 'max:30'],
            'stock'           => ['required', 'integer', 'min:0'],
            'minimum_order'   => ['required', 'integer', 'min:1'],
            'pickup_location' => ['nullable', 'string', 'max:200'],
            'category_id'     => ['nullable', 'exists:categories,id'],
            'harvest_date'    => ['nullable', 'date'],
            'tags'            => ['nullable', 'array'],
            'tags.*'          => ['exists:tags,id'],
            'new_tags'        => ['nullable', 'string', 'max:300'],
            'images'          => ['nullable', 'array', 'max:5'],
            'images.*'        => ['image', 'max:3072'],
        ]);

        $product->update([
            'category_id'     => $data['category_id'] ?? null,
            'title'           => $data['title'],
            'description'     => $data['description'],
            'price'           => $data['price'],
            'unit'            => $data['unit'],
            'stock'           => $data['stock'],
            'minimum_order'   => $data['minimum_order'],
            'pickup_location' => $data['pickup_location'] ?? null,
            'harvest_date'    => $data['harvest_date'] ?? null,
            'is_available'    => $request->boolean('is_available', true),
            'status'          => $data['stock'] > 10 ? 'in_stock' : ($data['stock'] > 0 ? 'low_stock' : 'out_of_stock'),
        ]);

        $tagIds = $data['tags'] ?? [];

        if (!empty($data['new_tags'])) {
            $names = array_filter(array_map('trim', explode(',', $data['new_tags'])));
            foreach ($names as $name) {
                $tag = Tag::firstOrCreate(
                    ['name' => $name, 'user_id' => auth()->id()],
                    ['slug' => \Illuminate\Support\Str::slug($name) . '-' . auth()->id()]
                );
                $tagIds[] = $tag->id;
            }
        }

        $product->tags()->sync($tagIds);

        // New images added on top of existing ones
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products/' . $product->id, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()
            ->route('products.show', $product->slug)
            ->with('status', 'Product updated.');
    }

    // Farmer: delete product (soft delete)
    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $product->delete();

        return redirect()->route('dashboard')->with('status', 'Product removed.');
    }

    // Farmer: toggle availability
    public function toggleAvailability(Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        $product->update(['is_available' => !$product->is_available]);

        return back()->with('status', 'Availability updated.');
    }
}