@extends('layouts.farmlink')

@section('title', 'Add Product | FARMLINK')

@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-slate-950">Add Product</h1>
        <p class="mt-3 text-slate-600">List fresh produce for buyers to discover and order.</p>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
              class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <x-input-label for="title" value="Product Name" />
                <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title') }}" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            {{-- Category --}}
            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">— No Category —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            {{-- Price + Unit --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="price" value="Price (₱)" />
                    <x-text-input id="price" name="price" type="number" step="0.01" min="0.01"
                        class="mt-1 block w-full" value="{{ old('price') }}" required />
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="unit" value="Unit (e.g. kg, bundle, piece)" />
                    <x-text-input id="unit" name="unit" class="mt-1 block w-full" value="{{ old('unit', 'kg') }}" required />
                    <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                </div>
            </div>

            {{-- Stock + Minimum --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="stock" value="Available Stock" />
                    <x-text-input id="stock" name="stock" type="number" min="0"
                        class="mt-1 block w-full" value="{{ old('stock', 0) }}" required />
                    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="minimum_order" value="Minimum Order" />
                    <x-text-input id="minimum_order" name="minimum_order" type="number" min="1"
                        class="mt-1 block w-full" value="{{ old('minimum_order', 1) }}" required />
                    <x-input-error :messages="$errors->get('minimum_order')" class="mt-2" />
                </div>
            </div>

            {{-- Pickup Location --}}
            <div>
                <x-input-label for="pickup_location" value="Pickup Area (optional)" />
                <x-text-input id="pickup_location" name="pickup_location" class="mt-1 block w-full"
                    value="{{ old('pickup_location') }}" placeholder="e.g. Barangay Lumbia, CDO" />
                <x-input-error :messages="$errors->get('pickup_location')" class="mt-2" />
            </div>

            {{-- Harvest Date --}}
            <div>
                <x-input-label for="harvest_date" value="Harvest Date (optional)" />
                <x-text-input id="harvest_date" name="harvest_date" type="date"
                    class="mt-1 block w-full" value="{{ old('harvest_date') }}" />
                <x-input-error :messages="$errors->get('harvest_date')" class="mt-2" />
            </div>

            {{-- Description --}}
            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required>{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            {{-- Tags --}}
            <div>
                <x-input-label value="Tags" />
                <p class="text-xs text-slate-400 mb-2">Select existing or type new ones (comma-separated)</p>
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach ($globalTags as $tag)
                        <label class="flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 text-sm cursor-pointer hover:bg-green-50">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                class="accent-green-600" />
                            {{ $tag->name }}
                        </label>
                    @endforeach
                    @foreach ($myTags as $tag)
                        <label class="flex items-center gap-1 rounded-full border border-green-300 bg-green-50 px-3 py-1 text-sm cursor-pointer">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                class="accent-green-600" />
                            {{ $tag->name }} <span class="text-green-500 text-xs">(mine)</span>
                        </label>
                    @endforeach
                </div>
                <x-input-label for="new_tags" value="New custom tags (comma-separated)" />
                <x-text-input id="new_tags" name="new_tags" class="mt-1 block w-full"
                    value="{{ old('new_tags') }}" placeholder="e.g. organic, heirloom, fresh-cut" />
                <x-input-error :messages="$errors->get('new_tags')" class="mt-2" />
            </div>

            {{-- Images --}}
            <div>
                <x-input-label for="images" value="Product Photos (up to 5)" />
                <input id="images" name="images[]" type="file" multiple accept="image/*"
                    class="mt-1 block w-full text-sm text-slate-600" />
                <p class="text-xs text-slate-400 mt-1">First image will be the primary display photo. Max 3MB each.</p>
                <x-input-error :messages="$errors->get('images')" class="mt-2" />
                <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
            </div>

            {{-- Availability toggle --}}
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_available" value="0" />
                <input type="checkbox" id="is_available" name="is_available" value="1"
                    {{ old('is_available', '1') == '1' ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 accent-green-600" />
                <x-input-label for="is_available" value="Mark as Available now" />
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('dashboard') }}"
                    class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit"
                    class="rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">Publish Product</button>
            </div>
        </form>
    </div>
</section>
@endsection