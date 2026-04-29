@extends('layouts.farmlink')

@section('title', 'Dashboard | FARMLINK')

@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        @if(session('status'))
            <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">
                {{ session('status') }}
            </div>
        @endif

        {{-- ==================== FARMER DASHBOARD ==================== --}}
        @if(auth()->user()->isFarmer())

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-4xl font-bold text-slate-950">Farmer Dashboard</h1>
                <p class="mt-2 text-slate-600">Welcome back, {{ auth()->user()->farm_name ?? auth()->user()->name }}.</p>
            </div>
            <a href="{{ route('products.create') }}"
               class="inline-block rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">
                + Add Product
            </a>
        </div>

        {{-- Stats --}}
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <strong class="text-3xl text-green-700">₱{{ number_format($totalRevenue, 2) }}</strong>
                <p class="mt-2 text-sm text-slate-500">Total Revenue (paid)</p>
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
                <strong class="text-3xl text-green-700">
                    {{ $avgRating ? number_format($avgRating, 1) . ' ★' : '—' }}
                </strong>
                <p class="mt-2 text-sm text-slate-500">Store Rating</p>
            </div>
        </div>

        {{-- Product table --}}
        <div class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-950">My Products</h2>
                @if($products->isNotEmpty())
                    <span class="text-sm text-slate-400">{{ $products->count() }} listing{{ $products->count() !== 1 ? 's' : '' }}</span>
                @endif
            </div>

            @if($products->isEmpty())
                <div class="mt-6 rounded-lg border-2 border-dashed border-slate-200 p-10 text-center">
                    <p class="text-slate-500">You haven't listed any products yet.</p>
                    <a href="{{ route('products.create') }}"
                       class="mt-3 inline-block text-sm font-semibold text-green-600 hover:underline">
                        Add your first product →
                    </a>
                </div>
            @else
            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="py-3 pr-4">Product</th>
                            <th class="pr-4">Stock</th>
                            <th class="pr-4">Price</th>
                            <th class="pr-4">Reviews</th>
                            <th class="pr-4">Status</th>
                            <th class="pr-4">Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($products as $product)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 pr-4 font-semibold text-slate-800">
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="hover:text-green-700 hover:underline">
                                    {{ $product->title }}
                                </a>
                            </td>
                            <td class="pr-4 text-slate-600">
                                {{ $product->stock }} {{ $product->unit }}
                                @if($product->isLowStock())
                                    <span class="ml-1 text-xs text-amber-500">⚠ Low</span>
                                @endif
                            </td>
                            <td class="pr-4 text-slate-600">₱{{ number_format($product->price, 2) }}</td>
                            <td class="pr-4 text-slate-600">
                                @if($product->reviews_count > 0)
                                    {{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="pr-4">
                                <span @class([
                                    'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                    'bg-green-100 text-green-700'   => $product->status === 'in_stock',
                                    'bg-yellow-100 text-yellow-700' => $product->status === 'low_stock',
                                    'bg-red-100 text-red-600'       => $product->status === 'out_of_stock',
                                ])>{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
                            </td>
                            <td class="pr-4">
                                <form method="POST" action="{{ route('products.toggle', $product->slug) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" @class([
                                        'rounded-full px-2.5 py-0.5 text-xs font-semibold cursor-pointer',
                                        'bg-green-100 text-green-700 hover:bg-green-200' => $product->is_available,
                                        'bg-slate-100 text-slate-500 hover:bg-slate-200' => !$product->is_available,
                                    ])>
                                        {{ $product->is_available ? 'Visible' : 'Hidden' }}
                                    </button>
                                </form>
                            </td>
                            <td class="flex items-center gap-3 py-3">
                                <a href="{{ route('products.edit', $product->slug) }}"
                                   class="text-xs font-medium text-slate-500 hover:text-green-700 hover:underline">Edit</a>
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="text-xs font-medium text-slate-500 hover:text-blue-600 hover:underline">View</a>
                                <form method="POST" action="{{ route('products.destroy', $product->slug) }}"
                                      onsubmit="return confirm('Delete \'{{ addslashes($product->title) }}\'? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs font-medium text-red-400 hover:text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- ==================== BUYER DASHBOARD ==================== --}}
        @elseif(auth()->user()->isBuyer())

        <div>
            <h1 class="text-4xl font-bold text-slate-950">My Orders</h1>
            <p class="mt-2 text-slate-600">Welcome back, {{ auth()->user()->name }}.</p>
        </div>

        @if($recentOrders->isEmpty())
            <div class="mt-8 rounded-lg border-2 border-dashed border-slate-200 p-10 text-center">
                <p class="text-slate-500">No orders yet.</p>
                <a href="{{ route('marketplace') }}"
                   class="mt-3 inline-block text-sm font-semibold text-green-600 hover:underline">
                    Browse the marketplace →
                </a>
            </div>
        @else
        <div class="mt-8 space-y-4">
            @foreach($recentOrders as $order)
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-semibold text-slate-800">Order #{{ $order->id }}</span>
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                        'bg-blue-100 text-blue-700'     => in_array($order->status, ['confirmed', 'preparing']),
                        'bg-purple-100 text-purple-700' => $order->status === 'out_for_delivery',
                        'bg-green-100 text-green-700'   => $order->status === 'delivered',
                        'bg-red-100 text-red-600'       => $order->status === 'cancelled',
                        'bg-slate-100 text-slate-600'   => !in_array($order->status, ['pending','confirmed','preparing','out_for_delivery','delivered','cancelled']),
                    ])>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                </div>
                <div class="mt-3 space-y-1">
                    @foreach($order->items as $item)
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>{{ $item->product->title ?? '[deleted product]' }} × {{ $item->quantity }}</span>
                            <span>₱{{ number_format($item->lineTotal(), 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 border-t border-slate-100 pt-3 flex justify-between text-sm font-semibold">
                    <span>Total</span>
                    <span class="text-green-700">₱{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ==================== ADMIN DASHBOARD ==================== --}}
        @elseif(auth()->user()->isAdmin())

        <div class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
            <h1 class="text-2xl font-bold text-slate-950">Admin Panel</h1>
            <p class="mt-2 text-slate-500">Manage farmer approvals and platform settings.</p>
            <a href="{{ route('admin.farmers.pending') }}"
               class="mt-5 inline-block rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                View Pending Farmers
            </a>
        </div>

        @endif

    </div>
</section>
@endsection