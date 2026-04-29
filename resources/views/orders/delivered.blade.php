@extends('layouts.farmlink')

@section('title', 'Delivered Orders | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-slate-950">My Cart & Orders</h1>
            <div class="mt-4 flex gap-3">
                <a class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200" href="{{ route('cart') }}">Cart</a>
                <a class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200" href="{{ route('orders.pending') }}">Pending</a>
                <a class="rounded-full bg-green-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('orders.delivered') }}">Delivered</a>
            </div>
            <div class="mt-8 grid gap-5">
                @foreach ($orders as $order)
                    <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">{{ $order['status'] }}</span>
                                <h2 class="mt-3 text-2xl font-bold text-slate-950">Order {{ $order['id'] }}</h2>
                                <p class="mt-1 text-sm text-slate-500">Delivered {{ $order['delivered_at'] ?? $order['created_at'] }}</p>
                            </div>
                            <strong class="text-xl text-green-700">&#8369;{{ number_format($order['totals']['total'], 2) }}</strong>
                        </div>
                        <div class="mt-5 grid gap-3">
                            @foreach ($order['items'] as $item)
                                <div class="flex flex-col gap-4 rounded-lg bg-slate-50 p-4 sm:flex-row sm:items-center">
                                    <img src="{{ $item['product']['image'] }}" alt="{{ $item['product']['name'] }}" class="h-24 w-32 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-slate-950">{{ $item['product']['name'] }}</h3>
                                        <p class="text-sm text-slate-500">{{ $item['quantity'] }} {{ $item['product']['unit'] }} from {{ $item['product']['farm'] }}</p>
                                    </div>
                                    <strong>&#8369;{{ number_format($item['line_total'], 2) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
