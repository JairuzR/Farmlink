@extends('layouts.farmlink')
@section('title', 'Edit Product | FARMLINK')
@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-slate-950">Edit Product</h1>
        <form method="POST" action="{{ route('products.update', $product->slug) }}" enctype="multipart/form-data"
              class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="title" value="Product Name" />
                <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title', $product->title) }}" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">— No Category —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="price" value="Price (₱)" />
                    <x-text-input id="price" name="price" type="number" step="0.01" min="0.01"
                        class="mt-1 block w-full" value="{{ old('price', $product->price) }}" required />
                </div>
                <div>
                    <x-input-label for="unit" value="Unit" />
                    <x-text-input id="unit" name="unit" class="mt-1 block w-full" value="{{ old('unit', $product->unit) }}" required />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="stock" value="Stock" />
                    <x-text-input id="stock" name="stock" type="number" min="0"
                        class="mt-1 block w-full" value="{{ old('stock', $product->stock) }}" required />
                </div>
                <div>
                    <x-input-label for="minimum_order" value="Minimum Order" />
                    <x-text-input id="minimum_order" name="minimum_order" type="number" min="1"
                        class="mt-1 block w-full" value="{{ old('minimum_order', $product->minimum_order) }}" required />
                </div>
            </div>

            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="5"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <x-input-label for="pickup_location" value="Pickup Location (optional)" />
                <x-text-input id="pickup_location" name="pickup_location" class="mt-1 block w-full"
                    value="{{ old('pickup_location', $product->pickup_location) }}" />
            </div>

            <div>
                <x-input-label for="harvest_date" value="Harvest Date (optional)" />
                <x-text-input id="harvest_date" name="harvest_date" type="date" class="mt-1 block w-full"
                    value="{{ old('harvest_date', $product->harvest_date?->format('Y-m-d')) }}" />
            </div>

            {{-- Tags --}}
            <div>
                <x-input-label value="Tags" />
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($globalTags->merge($myTags) as $tag)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ $product->tags->contains($tag->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-600">
                            <span class="text-sm">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-2">
                    <x-input-label for="new_tags" value="Add custom tags (comma-separated)" />
                    <x-text-input id="new_tags" name="new_tags" class="mt-1 block w-full"
                        placeholder="e.g. organic, heirloom, pesticide-free" value="{{ old('new_tags') }}" />
                </div>
            </div>

            {{-- Existing images --}}
            @if($product->images->isNotEmpty())
            <div>
                <p class="text-sm font-medium text-slate-700">Current Images</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($product->images as $img)
                        <img src="{{ asset('storage/'.$img->path) }}" class="h-20 w-20 rounded-lg object-cover ring-1 ring-slate-200">
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <x-input-label for="images" value="Add More Images (max 5 total)" />
                <input id="images" name="images[]" type="file" multiple accept="image/*"
                    class="mt-1 block w-full text-sm text-slate-600">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_available" name="is_available" value="1"
                    {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-green-600">
                <x-input-label for="is_available" value="Available for purchase" />
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                    Save Changes
                </button>
                <a href="{{ route('products.show', $product->slug) }}"
                   class="rounded-lg border border-slate-300 px-6 py-3 font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</section>
@endsection