@extends('layouts.farmlink')
@section('title', 'Incoming Orders | FARMLINK')

@section('content')
<section class="bg-slate-50 min-h-screen py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <h1 class="text-3xl font-bold text-slate-950 mb-6">Incoming Orders</h1>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
        @forelse($orders as $order)
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">

            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-sm font-bold text-slate-700">
                        ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}
                    </span>
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-yellow-100 text-yellow-700'  => $order->status === 'pending',
                        'bg-blue-100 text-blue-700'      => in_array($order->status, ['confirmed', 'preparing']),
                        'bg-purple-100 text-purple-700'  => $order->status === 'out_for_delivery',
                        'bg-green-100 text-green-700'    => $order->status === 'delivered',
                        'bg-red-100 text-red-700'        => $order->status === 'cancelled',
                    ])>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    <span class="text-sm text-slate-500">
                        from <strong>{{ $order->buyer->name }}</strong>
                    </span>
                </div>
                <span class="text-sm text-slate-400">{{ $order->created_at->format('M d, Y · g:i A') }}</span>
            </div>

            {{-- Items --}}
            <div class="divide-y divide-slate-100 px-6">
                @foreach($order->items as $item)
                @php $product = $item->product; @endphp
                <div class="flex items-center gap-4 py-4">
                    @php
                        $thumb = $product?->primaryImage
                            ? asset('storage/' . $product->primaryImage->path)
                            : 'https://placehold.co/80x80/e2e8f0/94a3b8?text=?';
                    @endphp
                    <img src="{{ $thumb }}" class="h-16 w-16 rounded-lg object-cover flex-shrink-0"
                         alt="{{ $product?->title ?? 'Product' }}">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 truncate">
                            {{ $product?->title ?? '[deleted product]' }}
                        </p>
                        <p class="text-sm text-slate-500">
                            {{ $item->quantity }} {{ $product?->unit ?? '' }}
                        </p>
                    </div>
                    <p class="font-semibold text-slate-800">
                        ₱{{ number_format($item->price_at_time * $item->quantity, 2) }}
                    </p>
                </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <div class="space-y-1 text-sm text-slate-500">
                    <p>{{ $order->delivery_address }}</p>
                    <p>
                        Payment: <span class="capitalize font-medium text-slate-700">{{ $order->payment_method }}</span>
                        &middot;
                        <span class="{{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }} font-medium capitalize">
                            {{ $order->payment_status }}
                        </span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <p class="font-bold text-green-700 text-lg">
                        ₱{{ number_format($order->total, 2) }}
                    </p>
                    <form method="POST" action="{{ route('orders.update-status', $order) }}"
                          class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <select name="status"
                            class="rounded-lg border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(['confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Update
                        </button>
                    </form>
                </div>
            </div>

        </div>
        @empty
        <div class="rounded-xl bg-white py-16 text-center shadow-sm ring-1 ring-slate-200">
            <h2 class="text-xl font-bold text-slate-800">No incoming orders yet</h2>
            <p class="mt-2 text-slate-500">When buyers order your products, they will appear here.</p>
        </div>
        @endforelse
        </div>

    </div>
</section>
@endsection