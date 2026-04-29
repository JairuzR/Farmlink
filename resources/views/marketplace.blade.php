@extends('layouts.farmlink')

@section('title', 'Marketplace | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-slate-950">Marketplace</h1>
                <p class="mt-3 text-slate-600">Fresh produce directly from verified local farmers</p>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @foreach ($categories as $filter)
                    <a href="{{ route('marketplace', ['category' => $filter]) }}" class="{{ $category === $filter ? 'bg-green-600 text-white' : 'bg-white text-slate-700' }} rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold shadow-sm">{{ $filter }}</a>
                @endforeach
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($products as $product)
                    <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                        <a href="{{ route('products.show', $product['slug']) }}">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-52 w-full object-cover">
                        </a>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-950">{{ $product['name'] }}</h2>
                                    <p class="mt-1 text-sm text-slate-500">{{ $product['farm'] }}</p>
                                </div>
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">{{ $product['status'] }}</span>
                            </div>
                            <div class="mt-5 flex items-center justify-between gap-3">
                                <strong class="text-xl text-green-700">&#8369;{{ number_format($product['price'], 0) }} / {{ $product['unit'] }}</strong>
                                <form method="POST" action="{{ route('cart.store') }}">
                                    @csrf
                                    <input type="hidden" name="product" value="{{ $product['slug'] }}">
                                    <input type="hidden" name="quantity" value="{{ $product['minimum'] }}">
                                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-lg bg-white p-10 text-center text-slate-600 shadow-sm ring-1 ring-slate-200">
                        No products found in this category.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
