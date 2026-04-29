@extends('layouts.farmlink')

@section('title', 'Your Cart | FARMLINK')

@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <h1 class="text-3xl font-bold text-slate-950 mb-8">Your Cart</h1>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-700 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if($items->isEmpty())
            <div class="rounded-lg bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-slate-500 text-lg">Your cart is empty.</p>
                <a href="{{ route('marketplace') }}"
                   class="mt-4 inline-block rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                    Browse Marketplace
                </a>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-[1fr_320px] items-start">

                {{-- Cart items --}}
                <div class="space-y-4">
                    @foreach($items as $item)
                    @php $product = $item->product; @endphp
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200 flex gap-5">

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
                               class="font-semibold text-slate-900 hover:text-green-700">
                                {{ $product->title }}
                            </a>
                            <p class="text-sm text-slate-500 mt-0.5">
                                by {{ $product->farmer->name }}
                                @if($product->farmer->farm_name)
                                    · {{ $product->farmer->farm_name }}
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
                                       class="w-20 rounded-lg border-slate-300 text-center text-sm"
                                       aria-label="Quantity">
                                <button type="submit"
                                    class="text-xs font-semibold text-slate-500 hover:text-green-700">
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
                                    class="text-xs text-red-400 hover:text-red-600 font-medium">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Order summary --}}
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sticky top-6">
                    <h2 class="text-xl font-bold text-slate-950 mb-4">Order Summary</h2>

                    <dl class="space-y-2 text-sm">
                        @foreach($items as $item)
                        <div class="flex justify-between text-slate-600">
                            <dt class="truncate max-w-[160px]">{{ $item->product->title }} × {{ $item->quantity }}</dt>
                            <dd>₱{{ number_format($item->subtotal(), 2) }}</dd>
                        </div>
                        @endforeach
                        <div class="border-t border-slate-100 pt-3 flex justify-between font-bold text-slate-900 text-base">
                            <dt>Total</dt>
                            <dd>₱{{ number_format($total, 2) }}</dd>
                        </div>
                    </dl>

                    <form method="POST" action="{{ route('checkout') }}" class="mt-6">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                                <option value="cod">Cash on Delivery</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Delivery Address</label>
                            <textarea name="delivery_address" rows="2" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Where should we deliver?">{{ auth()->user()->address }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">
                            Place Order
                        </button>
                    </form>
                </div>

            </div>
        @endif
    </div>
</section>
@endsection