@extends('layouts.farmlink')

@section('title', $product['name'].' | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_420px]">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-[520px] w-full rounded-lg object-cover">
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-slate-950">About this Produce</h2>
                        <p class="mt-3 leading-8 text-slate-600">{{ $product['description'] }}</p>
                    </div>
                </div>

                <aside class="self-start rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">{{ $product['status'] }}</span>
                    <h1 class="mt-4 text-4xl font-bold text-slate-950">{{ $product['name'] }}</h1>
                    <p class="mt-2 text-slate-600">{{ $product['farm'] }}</p>
                    <p class="mt-6 text-3xl font-bold text-green-700">&#8369;{{ number_format($product['price'], 0) }} / {{ $product['unit'] }}</p>

                    <dl class="mt-6 grid gap-3 text-sm">
                        <div class="flex justify-between rounded-lg bg-slate-50 p-3"><dt>Minimum order</dt><dd class="font-semibold">{{ $product['minimum'] }} {{ $product['unit'] }}</dd></div>
                        <div class="flex justify-between rounded-lg bg-slate-50 p-3"><dt>Available stock</dt><dd class="font-semibold">{{ $product['stock'] }} {{ $product['unit'] }}</dd></div>
                        <div class="flex justify-between rounded-lg bg-slate-50 p-3"><dt>Pickup area</dt><dd class="font-semibold">{{ $product['pickup'] }}</dd></div>
                    </dl>

                    <form method="POST" action="{{ route('cart.store') }}" class="mt-6 grid grid-cols-[120px_1fr] gap-3">
                        @csrf
                        <input type="hidden" name="product" value="{{ $product['slug'] }}">
                        <input class="rounded-lg border-slate-300" type="number" name="quantity" value="{{ $product['minimum'] }}" min="{{ $product['minimum'] }}" max="{{ $product['stock'] }}" aria-label="Quantity in {{ $product['unit'] }}">
                        <button type="submit" class="rounded-lg bg-green-600 px-5 py-3 text-center font-semibold text-white hover:bg-green-700">Add to Cart</button>
                    </form>
                    <a href="{{ route('farmers') }}" class="mt-3 block rounded-lg border border-green-600 px-5 py-3 text-center font-semibold text-green-700 hover:bg-green-50">Contact Farmer</a>
                </aside>
            </div>
        </div>
    </section>
@endsection
