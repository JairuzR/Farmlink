@extends('layouts.farmlink')

@section('title', 'Dashboard | FARMLINK')

@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        @if(auth()->user()->isFarmer())

        {{-- Farmer Dashboard --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-4xl font-bold text-slate-950">Farmer Dashboard</h1>
                <p class="mt-2 text-slate-600">Welcome back, {{ auth()->user()->farm_name ?? auth()->user()->name }}.</p>
            </div>
            <a href="{{ route('products.create') }}" class="rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">
                + Add Product
            </a>
        </div>

        {{-- Stats --}}
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <strong class="text-3xl text-green-700">₱{{ number_format($totalRevenue, 2) }}</strong>
                <p class="mt-2 text-sm text-slate-500">Total Revenue</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <strong class="text-3xl text-green-700">{{ $products->count() }}</strong>
                <p class="mt-2 text-sm text-slate-500">Listed Products</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <strong class="text-3xl text-green-700">{{ $activeOrders }}</strong>
                <p class="mt-2 text-sm text-slate-500">Active Orders</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <strong class="text-3xl text-green-700">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</strong>
                <p class="mt-2 text-sm text-slate-500">Store Rating</p>
            </div>
        </div>

        {{-- Product table --}}
        <div class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-xl font-bold text-slate-950">My Products</h2>
            @if($products->isEmpty())
                <p class="mt-4 text-slate-500">You haven't listed any products yet. <a href="{{ route('products.create') }}" class="text-green-600 hover:underline">Add your first one!</a></p>
            @else
            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="py-3 pr-4">Product</th>
                            <th class="pr-4">Stock</th>
                            <th class="pr-4">Price</th>
                            <th class="pr-4">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($products as $product)
                        <tr>
                            <td class="py-3 pr-4 font-semibold">{{ $product->title }}</td>
                            <td class="pr-4">{{ $product->stock }} {{ $product->unit }}</td>
                            <td class="pr-4">₱{{ number_format($product->price, 2) }}</td>
                            <td class="pr-4">
                                <span @class([
                                    'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                    'bg-green-100 text-green-700'  => $product->status === 'in_stock',
                                    'bg-yellow-100 text-yellow-700'=> $product->status === 'low_stock',
                                    'bg-red-100 text-red-600'      => $product->status === 'out_of_stock',
                                ])>{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
                            </td>
                            <td class="flex items-center gap-2 py-3">
                                <a href="{{ route('products.edit', $product->slug) }}"
                                   class="text-xs text-slate-500 hover:text-green-700 underline">Edit</a>
                                <form method="POST" action="{{ route('products.toggle', $product->slug) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-slate-500 hover:text-amber-600 underline">
                                        {{ $product->is_available ? 'Hide' : 'Show' }}
                                    </button>
                                </form>
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="text-xs text-slate-500 hover:text-blue-600 underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        @elseif(auth()->user()->isBuyer())

        {{-- Buyer Dashboard --}}
        <h1 class="text-4xl font-bold text-slate-950">My Orders</h1>
        <p class="mt-2 text-slate-600">Welcome back, {{ auth()->user()->name }}.</p>

        @if($recentOrders->isEmpty())
            <div class="mt-8 rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-slate-500">No orders yet. <a href="{{ route('marketplace') }}" class="text-green-600 hover:underline">Browse the marketplace!</a></p>
            </div>
        @else
        <div class="mt-8 space-y-4">
            @foreach($recentOrders as $order)
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-800">Order #{{ $order->id }}</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-slate-500">{{ $order->items->count() }} item(s) · ₱{{ number_format($order->total, 2) }}</p>
            </div>
            @endforeach
        </div>
        @endif

        @elseif(auth()->user()->isAdmin())

        {{-- Admin redirect --}}
        <div class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
            <h1 class="text-2xl font-bold text-slate-950">Admin Panel</h1>
            <a href="{{ route('admin.farmers.pending') }}" class="mt-4 inline-block rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                View Pending Farmers
            </a>
        </div>

        @endif

    </div>
</section>
@endsection