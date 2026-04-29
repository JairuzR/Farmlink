@extends('layouts.farmlink')

@section('title', 'Pending Orders | FARMLINK')

@section('content')
<section class="bg-slate-50 min-h-screen py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <h1 class="text-3xl font-bold text-slate-950">My Cart & Orders</h1>

        {{-- Tab bar --}}
        <div class="mt-4 flex gap-2 border-b border-slate-200">
            <a href="{{ route('cart') }}"
               class="flex items-center gap-1.5 border-b-2 border-transparent px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700">
                🛒 Cart
                @php($cartCount = auth()->user()->cartItems()->count())
                @if($cartCount > 0)
                    <span class="rounded-full bg-slate-200 px-2 py-0.5 text-xs">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="{{ route('orders.pending') }}"
               class="flex items-center gap-1.5 border-b-2 border-green-600 px-4 py-2.5 text-sm font-medium text-green-700">
                Pending
                @if($orders->isNotEmpty())
                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">{{ $orders->count() }}</span>
                @endif
            </a>
            <a href="{{ route('orders.delivered') }}"
               class="flex items-center gap-1.5 border-b-2 border-transparent px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700">
                Delivered
            </a>
        </div>

        @if(session('success'))
            <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 space-y-4">
            @forelse($orders as $order)
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">

                {{-- Order header --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-sm font-bold text-slate-700">ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</span>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                            'bg-blue-100 text-blue-700'     => in_array($order->status, ['confirmed','preparing']),
                            'bg-purple-100 text-purple-700' => $order->status === 'out_for_delivery',
                        ])>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-slate-500">{{ $order->created_at->format('M d, Y · g:i A') }}</span>
                        <form method="POST" action="{{ route('orders.mark-delivered', $order) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                Mark Delivered
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Items --}}
                <div class="divide-y divide-slate-100 px-6">
                    @foreach($order->items as $item)
                    @php $product = $item->product; @endphp
                    <div class="flex items-center gap-4 py-4">
                        @php
                            $thumb = $product->primaryImage
                                ? asset('storage/' . $product->primaryImage->path)
                                : 'https://placehold.co/80x80/e2e8f0/94a3b8?text=?';
                        @endphp
                        <img src="{{ $thumb }}" class="h-16 w-16 rounded-lg object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 truncate">
                                {{ $product->title ?? '[deleted product]' }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ $item->quantity }} {{ $product->unit ?? '' }}
                                · by {{ $product->farmer->name ?? '—' }}
                            </p>
                        </div>
                        <p class="font-semibold text-slate-800">
                            ₱{{ number_format($item->price_at_time * $item->quantity, 2) }}
                        </p>
                    </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50 px-6 py-3">
                    <div class="text-sm text-slate-500">
                        📍 {{ $order->delivery_address }}
                        · <span class="capitalize">{{ $order->payment_method }}</span>
                    </div>
                    <p class="font-bold text-green-700">
                        Total: ₱{{ number_format($order->total, 2) }}
                    </p>
                </div>
            </div>
            @empty
            <div class="rounded-xl bg-white py-16 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-4xl">📦</p>
                <h2 class="mt-4 text-xl font-bold text-slate-800">No pending orders</h2>
                <p class="mt-2 text-slate-500">Checkout from your cart to create an order.</p>
                <a href="{{ route('marketplace') }}"
                   class="mt-5 inline-block rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                    Browse Marketplace
                </a>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection