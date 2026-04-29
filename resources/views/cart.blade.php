@extends('layouts.farmlink')

@section('title', 'My Cart & Orders | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-slate-950">My Cart & Orders</h1>
            <div class="mt-4 flex gap-3">
                <a class="rounded-full bg-green-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('cart') }}">Cart</a>
                <a class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200" href="{{ route('orders.pending') }}">Pending</a>
                <a class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200" href="{{ route('orders.delivered') }}">Delivered</a>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    @forelse ($items as $item)
                        @php($product = $item['product'])
                        <div class="flex flex-col gap-5 border-b border-slate-100 py-5 first:pt-0 last:border-0 last:pb-0 sm:flex-row">
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-32 w-40 rounded-lg object-cover">
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-slate-950">{{ $product['name'] }}</h2>
                                <p class="mt-1 text-sm text-slate-500">{{ $product['farm'] }}</p>
                                <p class="mt-4 font-semibold text-green-700">&#8369;{{ number_format($product['price'], 0) }} / {{ $product['unit'] }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <form method="POST" action="{{ route('cart.update', $product['slug']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}">
                                    <button type="submit" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200">-</button>
                                </form>
                                <span class="min-w-14 text-center font-semibold">{{ $item['quantity'] }} {{ $product['unit'] }}</span>
                                <form method="POST" action="{{ route('cart.update', $product['slug']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                    <button type="submit" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200">+</button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $product['slug']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Remove</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <h2 class="text-2xl font-bold text-slate-950">Your cart is empty</h2>
                            <p class="mt-2 text-slate-600">Add fresh produce from the marketplace to begin an order.</p>
                            <a href="{{ route('marketplace') }}" class="mt-6 inline-flex rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">Browse Marketplace</a>
                        </div>
                    @endforelse
                </div>

                <aside class="self-start rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-xl font-bold text-slate-950">Order Summary</h2>
                    <div class="mt-5 grid gap-3 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><strong>&#8369;{{ number_format($totals['subtotal'], 2) }}</strong></div>
                        <div class="flex justify-between"><span>Delivery fee</span><strong>&#8369;{{ number_format($totals['delivery'], 2) }}</strong></div>
                        <div class="flex justify-between border-t border-slate-200 pt-3 text-lg"><span>Total</span><strong class="text-green-700">&#8369;{{ number_format($totals['total'], 2) }}</strong></div>
                    </div>
                    <form method="POST" action="{{ route('checkout') }}">
                        @csrf
                        <button type="submit" @disabled($items === []) class="mt-6 block w-full rounded-lg bg-green-600 px-5 py-3 text-center font-semibold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-slate-300">Proceed to Checkout</button>
                    </form>
                </aside>
            </div>
        </div>
    </section>
@endsection
