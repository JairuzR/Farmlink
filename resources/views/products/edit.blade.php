@extends('layouts.farmlink')
@section('title', 'Edit Product | FARMLINK')

@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between">
            <h1 class="text-4xl font-bold text-slate-950">Edit Product</h1>
            <a href="{{ route('products.show', $product->slug) }}"
               class="text-sm text-slate-500 hover:text-slate-700 hover:underline">← Back to listing</a>
        </div>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-700 ring-1 ring-red-200">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.update', $product->slug) }}"
              enctype="multipart/form-data"
              class="mt-8 space-y-6 rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
            @csrf
            @method('PATCH')

            {{-- Title --}}
            <div>
                <x-input-label for="title" value="Product Name" />
                <x-text-input id="title" name="title" class="mt-1 block w-full"
                    value="{{ old('title', $product->title) }}" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            {{-- Category --}}
            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">— No Category —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            @selected(old('category_id', $product->category_id) == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Price & Unit --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="price" value="Price (₱)" />
                    <x-text-input id="price" name="price" type="number" step="0.01" min="0.01"
                        class="mt-1 block w-full"
                        value="{{ old('price', $product->price) }}" required />
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="unit" value="Unit (e.g. kg, bundle)" />
                    <x-text-input id="unit" name="unit" class="mt-1 block w-full"
                        value="{{ old('unit', $product->unit) }}" required />
                    <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                </div>
            </div>

            {{-- Stock & Minimum Order --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="stock" value="Stock" />
                    <x-text-input id="stock" name="stock" type="number" min="0"
                        class="mt-1 block w-full"
                        value="{{ old('stock', $product->stock) }}" required />
                    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="minimum_order" value="Minimum Order" />
                    <x-text-input id="minimum_order" name="minimum_order" type="number" min="1"
                        class="mt-1 block w-full"
                        value="{{ old('minimum_order', $product->minimum_order) }}" required />
                    <x-input-error :messages="$errors->get('minimum_order')" class="mt-2" />
                </div>
            </div>

            {{-- Description --}}
            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="5"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $product->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            {{-- Pickup Location --}}
            <div>
                <x-input-label for="pickup_location" value="Pickup Location (optional)" />
                <x-text-input id="pickup_location" name="pickup_location" class="mt-1 block w-full"
                    value="{{ old('pickup_location', $product->pickup_location) }}" />
            </div>

            {{-- Harvest Date --}}
            <div>
                <x-input-label for="harvest_date" value="Harvest Date (optional)" />
                <x-text-input id="harvest_date" name="harvest_date" type="date" class="mt-1 block w-full"
                    value="{{ old('harvest_date', $product->harvest_date?->format('Y-m-d')) }}" />
            </div>

            {{-- Tags --}}
            <div>
                <x-input-label value="Tags" />
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach($globalTags->merge($myTags) as $tag)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tags', $product->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-slate-700">{{ $tag->name }}</span>
                            @if($tag->user_id)
                                <span class="text-xs text-slate-400">(custom)</span>
                            @endif
                        </label>
                    @endforeach
                </div>
                <div class="mt-3">
                    <x-input-label for="new_tags" value="Add new custom tags (comma-separated)" />
                    <x-text-input id="new_tags" name="new_tags" class="mt-1 block w-full"
                        placeholder="e.g. organic, heirloom, pesticide-free"
                        value="{{ old('new_tags') }}" />
                </div>
            </div>

            {{-- Existing images with delete --}}
            @if($product->images->isNotEmpty())
            <div>
                <x-input-label value="Current Images" />
                <p class="mt-1 text-xs text-slate-400">Check the box on an image to remove it when you save.</p>
                <div class="mt-2 flex flex-wrap gap-3">
                    @foreach($product->images as $img)
                    <div class="relative">
                        <img src="{{ asset('storage/'.$img->path) }}"
                             class="h-24 w-24 rounded-lg object-cover ring-1 ring-slate-200">
                        <label class="absolute -top-2 -right-2 flex h-5 w-5 cursor-pointer items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600"
                               title="Remove this image">
                            <input type="checkbox" name="delete_images[]"
                                   value="{{ $img->id }}" class="sr-only">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Upload new images --}}
            <div>
                <x-input-label for="images" value="Add More Images (up to 5 total, max 3MB each)" />
                <input id="images" name="images[]" type="file" multiple accept="image/*"
                    class="mt-1 block w-full text-sm text-slate-600"
                    onchange="previewNewImages(this)">
                <div id="new-image-previews" class="mt-2 flex flex-wrap gap-2"></div>
            </div>

            {{-- Availability toggle --}}
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 p-4">
                <input type="checkbox" id="is_available" name="is_available" value="1"
                    {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <div>
                    <x-input-label for="is_available" value="Available for purchase" />
                    <p class="text-xs text-slate-400">Uncheck to hide this product from the marketplace.</p>
                </div>
            </div>

            {{-- Actions --}}
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

<script>
function previewNewImages(input) {
    const container = document.getElementById('new-image-previews');
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'h-20 w-20 rounded-lg object-cover ring-1 ring-slate-200';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endsection