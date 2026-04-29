@extends('layouts.farmlink')
@use('Illuminate\Support\Facades\Storage')

@section('title', 'Incoming Orders | FARMLINK')

@section('content')
<section class="bg-slate-50 min-h-screen py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-950">Incoming Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Manage and update the status of your orders.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-4xl mb-3">📦</p>
                <p class="text-lg font-semibold text-slate-700">No incoming orders yet</p>
                <p class="mt-1 text-sm text-slate-500">When buyers place orders, they'll show up here.</p>
                <a href="{{ route('marketplace') }}"
                   class="mt-4 inline-block text-sm font-semibold text-green-600 hover:underline">
                    View your products in the marketplace →
                </a>
            </div>
        @else
            <div class="space-y-6">
                @foreach($orders as $order)
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">

                    {{-- Order header --}}
                    <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-bold text-slate-800">
                                ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Placed {{ $order->created_at->diffForHumans() }} by
                                <span class="font-medium text-slate-700">{{ $order->buyer->name }}</span>
                                ({{ $order->buyer->phone ?? 'no phone' }})
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Payment badge --}}
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700'   => $order->payment_status === 'paid',
                                'bg-yellow-100 text-yellow-700' => $order->payment_status === 'unpaid',
                            ])>
                                {{ ucfirst($order->payment_status) }}
                                · {{ strtoupper($order->payment_method) }}
                            </span>

                            {{-- Status badge --}}
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-yellow-100 text-yellow-700'   => $order->status === 'pending',
                                'bg-blue-100 text-blue-700'       => $order->status === 'confirmed',
                                'bg-indigo-100 text-indigo-700'   => $order->status === 'preparing',
                                'bg-purple-100 text-purple-700'   => $order->status === 'out_for_delivery',
                                'bg-green-100 text-green-700'     => $order->status === 'delivered',
                                'bg-red-100 text-red-600'         => $order->status === 'cancelled',
                            ])>
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                    </div>

                    {{-- Order items --}}
                    <div class="divide-y divide-slate-100 px-6">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-4 py-4">
                            {{-- Product image --}}
                            @if($item->product->primaryImage)
                                <img src="{{ Storage::url($item->product->primaryImage->path) }}"
                                     alt="{{ $item->product->title }}"
                                     class="h-14 w-14 rounded-lg object-cover shrink-0">
                            @else
                                <div class="h-14 w-14 rounded-lg bg-slate-100 shrink-0 grid place-items-center text-slate-400 text-xl">🌾</div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ $item->product->title }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ $item->quantity }} {{ $item->product->unit }}
                                    × ₱{{ number_format($item->price_at_time, 2) }}
                                </p>
                            </div>
                            <p class="font-bold text-slate-800 shrink-0">
                                ₱{{ number_format($item->quantity * $item->price_at_time, 2) }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Order footer --}}
                    <div class="flex flex-col gap-4 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-slate-600 space-y-1">
                            <p><span class="font-medium">Deliver to:</span> {{ $order->delivery_address }}</p>
                            <p class="font-bold text-slate-800">
                                Total: ₱{{ number_format($order->total, 2) }}
                                <span class="text-xs font-normal text-slate-400 ml-1">
                                    (your cut: ₱{{ number_format($order->total * 0.95, 2) }})
                                </span>
                            </p>
                        </div>

                        {{-- Status update form --}}
                        @if(!in_array($order->status, ['delivered', 'cancelled']))
                        <form method="POST" action="{{ route('orders.update-status', $order) }}"
                              class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="status"
                                class="rounded-lg border-slate-300 text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                                @php
                                    $next = [
                                        'pending'          => ['confirmed' => 'Confirm Order', 'cancelled' => 'Cancel'],
                                        'confirmed'        => ['preparing' => 'Mark Preparing', 'cancelled' => 'Cancel'],
                                        'preparing'        => ['out_for_delivery' => 'Out for Delivery'],
                                        'out_for_delivery' => ['delivered' => 'Mark Delivered'],
                                    ];
                                    $options = $next[$order->status] ?? [];
                                @endphp
                                @foreach($options as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">
                                Update
                            </button>
                        </form>
                        @else
                            <span class="text-sm text-slate-400 italic">
                                {{ $order->status === 'delivered' ? 'Order completed ✓' : 'Order cancelled' }}
                            </span>
                        @endif
                    </div>

                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection