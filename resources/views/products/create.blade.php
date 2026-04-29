@extends('layouts.farmlink')

@section('title', 'Add Product | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-slate-950">Add Product</h1>
            <p class="mt-3 text-slate-600">List fresh produce in the marketplace for buyers to order.</p>

            <form method="POST" action="{{ route('products.store') }}" class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="name" value="Product Name" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name') }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="farm" value="Farm Name" />
                        <x-text-input id="farm" name="farm" class="mt-1 block w-full" value="{{ old('farm', Auth::user()->name.' Farm') }}" required />
                        <x-input-error :messages="$errors->get('farm')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="price" value="Price" />
                        <x-text-input id="price" name="price" type="number" min="1" step="0.01" class="mt-1 block w-full" value="{{ old('price') }}" required />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="unit" value="Unit" />
                        <x-text-input id="unit" name="unit" class="mt-1 block w-full" value="{{ old('unit', 'kg') }}" required />
                        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="category" value="Category" />
                        <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            @foreach ($categories as $option)
                                <option @selected(old('category') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="stock" value="Available Stock" />
                        <x-text-input id="stock" name="stock" type="number" min="1" class="mt-1 block w-full" value="{{ old('stock') }}" required />
                        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="minimum" value="Minimum Order" />
                        <x-text-input id="minimum" name="minimum" type="number" min="1" class="mt-1 block w-full" value="{{ old('minimum', 1) }}" required />
                        <x-input-error :messages="$errors->get('minimum')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="pickup" value="Pickup Area" />
                        <x-text-input id="pickup" name="pickup" class="mt-1 block w-full" value="{{ old('pickup') }}" required />
                        <x-input-error :messages="$errors->get('pickup')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="image" value="Image URL" />
                        <x-text-input id="image" name="image" type="url" class="mt-1 block w-full" value="{{ old('image') }}" placeholder="Optional" />
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">Publish Product</button>
                </div>
            </form>
        </div>
    </section>
@endsection
