@extends('layouts.farmlink')

@section('title', 'Your Cart | FARMLINK')

@section('content')
<section class="bg-slate-50 min-h-screen py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-950">Your Cart</h1>
            @if($items->isNotEmpty())
                <p class="mt-1 text-sm text-slate-500">{{ $items->count() }} {{ Str::plural('item', $items->count()) }} from local farmers</p>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if($items->isEmpty())

            {{-- Empty state --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
                <div class="grid md:grid-cols-2 min-h-[420px]">

                    {{-- Left: illustration panel --}}
                    <div class="flex flex-col items-center justify-center bg-gradient-to-br from-green-50 to-emerald-100 p-12 text-center">
                        <div class="relative mb-6">
                            {{-- Cart icon, drawn in SVG --}}
                            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-green-100">
                                <svg class="h-14 w-14 text-green-400" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 6h15l-2 8H8L6 6Z" />
                                    <path d="M6 6 5 3H2" />
                                    <circle cx="9" cy="20" r="1" />
                                    <circle cx="18" cy="20" r="1" />
                                </svg>
                            </div>
                            {{-- Small floating badge --}}
                            {{-- <span class="absolute -right-1 -top-1 flex h-7 w-7 items-center justify-center rounded-full bg-green-600 text-xs font-bold text-white shadow">
                                0
                            </span> --}}
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">Your cart is empty</h2>
                        <p class="mt-2 max-w-xs text-sm leading-relaxed text-slate-500">
                            Browse fresh produce from local Filipino farmers and add items to your cart.
                        </p>
                    </div>

                    {{-- Right: quick actions --}}
                    <div class="flex flex-col justify-center gap-5 p-10">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Where to next?</p>
                        </div>

                        <a href="{{ route('marketplace') }}"
                           class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm transition hover:border-green-400 hover:shadow-md">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 9h18M3 9l2-4h14l2 4M3 9v10a1 1 0 001 1h16a1 1 0 001-1V9" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">Browse Marketplace</p>
                                <p class="text-xs text-slate-500">Find fresh produce from local farms</p>
                            </div>
                            <svg class="ml-auto h-4 w-4 text-slate-300 group-hover:text-green-500 transition" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </a>

                        <a href="{{ route('farmers') }}"
                           class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm transition hover:border-green-400 hover:shadow-md">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M17 20H7a4 4 0 01-4-4V9.5a4 4 0 012-3.46l5-2.89a4 4 0 014 0l5 2.89a4 4 0 012 3.46V16a4 4 0 01-4 4Z" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">Find Farmers</p>
                                <p class="text-xs text-slate-500">See farmers near your area</p>
                            </div>
                            <svg class="ml-auto h-4 w-4 text-slate-300 group-hover:text-green-500 transition" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </a>

                        @auth
                            <a href="{{ route('orders.pending') }}"
                               class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm transition hover:border-green-400 hover:shadow-md">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">View Orders</p>
                                    <p class="text-xs text-slate-500">Track your pending and past orders</p>
                                </div>
                                <svg class="ml-auto h-4 w-4 text-slate-300 group-hover:text-green-500 transition" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        @endauth
                    </div>

                </div>
            </div>

        @else

            <div class="grid gap-8 lg:grid-cols-[1fr_320px] items-start">

                {{-- Cart items --}}
                <div class="space-y-4">
                    @foreach($items as $item)
                    @php $product = $item->product; @endphp
                    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 flex gap-5 transition hover:shadow-md">

                        {{-- Thumbnail --}}
                        @php
                            $thumb = $product->primaryImage
                                ? asset('storage/' . $product->primaryImage->path)
                                : 'https://placehold.co/100x100/e2e8f0/94a3b8?text=?';
                        @endphp
                        <img src="{{ $thumb }}" alt="{{ $product->title }}"
                             class="h-24 w-24 rounded-lg object-cover flex-shrink-0">

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="font-semibold text-slate-900 hover:text-green-700 transition">
                                {{ $product->title }}
                            </a>
                            <p class="text-sm text-slate-500 mt-0.5">
                                by {{ $product->farmer->name }}
                                @if($product->farmer->farm_name)
                                    &middot; {{ $product->farmer->farm_name }}
                                @endif
                            </p>
                            <p class="text-sm text-green-700 font-semibold mt-1">
                                ₱{{ number_format($product->price, 2) }} / {{ $product->unit }}
                            </p>

                            {{-- Quantity update --}}
                            <form method="POST" action="{{ route('cart.update', $product) }}"
                                  class="mt-3 flex items-center gap-3">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity"
                                       value="{{ $item->quantity }}"
                                       min="1" max="{{ $product->stock }}"
                                       class="w-20 rounded-lg border-slate-300 text-center text-sm focus:border-green-500 focus:ring-green-500"
                                       aria-label="Quantity">
                                <button type="submit"
                                    class="text-xs font-semibold text-slate-400 hover:text-green-700 transition">
                                    Update
                                </button>
                            </form>
                        </div>

                        {{-- Subtotal + remove --}}
                        <div class="flex flex-col items-end justify-between flex-shrink-0">
                            <p class="font-bold text-slate-900 text-lg">
                                ₱{{ number_format($item->subtotal(), 2) }}
                            </p>
                            <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs text-red-400 hover:text-red-600 font-medium transition">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Order summary --}}
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sticky top-6">
                    <h2 class="text-lg font-bold text-slate-950 mb-4">Order Summary</h2>

                    <dl class="space-y-2 text-sm">
                        @foreach($items as $item)
                        <div class="flex justify-between text-slate-600">
                            <dt class="truncate max-w-[160px]">
                                {{ $item->product->title }} &times; {{ $item->quantity }}
                            </dt>
                            <dd>₱{{ number_format($item->subtotal(), 2) }}</dd>
                        </div>
                        @endforeach

                        <div class="border-t border-slate-100 pt-3 flex justify-between text-slate-500 text-xs">
                            <dt>Delivery fee</dt>
                            <dd>Free</dd>
                        </div>

                        <div class="border-t border-slate-200 pt-3 flex justify-between font-bold text-slate-900 text-base">
                            <dt>Total</dt>
                            <dd>₱{{ number_format($total, 2) }}</dd>
                        </div>
                    </dl>

                    <form method="POST" action="{{ route('checkout') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                                <option value="cod">Cash on Delivery</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Delivery Address</label>
                            <textarea name="delivery_address" rows="2" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Where should we deliver?">{{ auth()->user()->address }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700 transition">
                            Place Order
                        </button>
                    </form>

                    <p class="mt-4 text-center text-xs text-slate-400">
                        A 5% platform fee is included in the total.
                    </p>
                </div>

            </div>
        @endif
    </div>
</section>
@endsection