@extends('layouts.farmlink')

@section('title', 'Pending Orders | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-slate-950">My Cart & Orders</h1>
            <div class="mt-4 flex gap-3">
                <a class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200" href="{{ route('cart') }}">Cart</a>
                <a class="rounded-full bg-green-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('orders.pending') }}">Pending</a>
                <a class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200" href="{{ route('orders.delivered') }}">Delivered</a>
            </div>

            <div class="mt-8 grid gap-5">
                @forelse ($orders as $order)
                    <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700">{{ $order['status'] }}</span>
                                <h2 class="mt-3 text-2xl font-bold text-slate-950">Order {{ $order['id'] }}</h2>
                                <p class="mt-1 text-sm text-slate-500">Placed {{ $order['created_at'] }}</p>
                            </div>
                            <form method="POST" action="{{ route('orders.mark-delivered', $order['id']) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">Mark Delivered</button>
                            </form>
                        </div>

                        <div class="mt-5 grid gap-3">
                            @foreach ($order['items'] as $item)
                                <div class="flex items-center justify-between rounded-lg bg-slate-50 p-4">
                                    <span>{{ $item['product']['name'] }} x {{ $item['quantity'] }} {{ $item['product']['unit'] }}</span>
                                    <strong>&#8369;{{ number_format($item['line_total'], 2) }}</strong>
                                </div>
                            @endforeach
                            <div class="flex justify-between pt-2 text-lg">
                                <span>Total</span>
                                <strong class="text-green-700">&#8369;{{ number_format($order['totals']['total'], 2) }}</strong>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
                        <p class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-amber-100 text-amber-700">…</p>
                        <h2 class="mt-4 text-2xl font-bold text-slate-950">No pending orders</h2>
                        <p class="mt-2 text-slate-600">Checkout from your cart to create a pending order.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
