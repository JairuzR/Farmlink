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

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-950">My Account</h1>
                <p class="mt-1 text-slate-500">Welcome back, {{ auth()->user()->name }}.</p>
            </div>
            <a href="{{ route('marketplace') }}"
               class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                Browse Marketplace
            </a>
        </div>

        {{-- Top stat cards --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            {{-- Profile card --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 flex items-center gap-4">
                <div class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-green-100 text-2xl">
                    👤
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-slate-500">Customer</p>
                    @if(auth()->user()->email)
                        <p class="mt-1 text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    @endif
                    @if(auth()->user()->phone)
                        <p class="text-xs text-slate-400">{{ auth()->user()->phone }}</p>
                    @endif
                    @if(auth()->user()->address)
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->address }}</p>
                    @endif
                </div>
            </div>

            {{-- Pending orders --}}
            <a href="{{ route('orders.pending') }}"
               class="rounded-xl bg-orange-50 p-5 shadow-sm ring-1 ring-orange-100 hover:ring-orange-300 transition group">
                <div class="flex items-start justify-between">
                    <span class="text-2xl">📦</span>
                    <span class="text-3xl font-bold text-orange-500">{{ $pendingOrders }}</span>
                </div>
                <p class="mt-3 font-semibold text-slate-800">Pending Orders</p>
                <p class="text-sm text-slate-500 group-hover:text-orange-600">Click to view details</p>
            </a>

            {{-- Delivered orders --}}
            <a href="{{ route('orders.delivered') }}"
               class="rounded-xl bg-green-50 p-5 shadow-sm ring-1 ring-green-100 hover:ring-green-300 transition group">
                <div class="flex items-start justify-between">
                    <span class="text-2xl">✅</span>
                    <span class="text-3xl font-bold text-green-600">{{ $deliveredOrders }}</span>
                </div>
                <p class="mt-3 font-semibold text-slate-800">Completed Orders</p>
                <p class="text-sm text-slate-500 group-hover:text-green-600">Click to view history</p>
            </a>
        </div>

        {{-- Stats + Settings --}}
        <div class="mt-6 grid gap-6 lg:grid-cols-2">

            {{-- Order Statistics --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Order Statistics</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                        <span class="text-sm text-slate-600">Total Orders</span>
                        <span class="font-bold text-slate-900">{{ $totalOrders }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-orange-50 px-4 py-3">
                        <span class="text-sm text-slate-600">In Progress</span>
                        <span class="font-bold text-orange-500">{{ $pendingOrders }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm text-slate-600">Completed</span>
                        <span class="font-bold text-green-600">{{ $deliveredOrders }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                        <span class="text-sm text-slate-600">Total Spent</span>
                        <span class="font-bold text-green-700">₱{{ number_format($totalSpent, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Account Settings --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Account Settings</h2>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center justify-between rounded-lg px-4 py-3 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">👤</span>
                            <span class="text-sm font-medium text-slate-700">Edit Profile</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('orders.pending') }}"
                       class="flex items-center justify-between rounded-lg px-4 py-3 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">📍</span>
                            <span class="text-sm font-medium text-slate-700">My Orders</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('2fa.setup') }}"
                       class="flex items-center justify-between rounded-lg px-4 py-3 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">🔒</span>
                            <span class="text-sm font-medium text-slate-700">Two-Factor Auth</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Promo: become a farmer --}}
        <div class="mt-6 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-6 ring-1 ring-green-200">
            <p class="text-lg">🌾 <span class="font-semibold text-green-800">Are you a farmer?</span></p>
            <p class="mt-1 text-sm text-slate-600">Switch to farmer view to manage your products, track sales, and connect with customers!</p>
            <a href="{{ route('register') }}"
               class="mt-4 inline-block rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                Register as Farmer
            </a>
        </div>

        {{-- Recent orders list --}}
        @if($recentOrders->isNotEmpty())
        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900">Recent Orders</h2>
                <a href="{{ route('orders.pending') }}" class="text-sm font-medium text-green-600 hover:underline">View all →</a>
            </div>
            <div class="space-y-3">
                @foreach($recentOrders as $order)
                <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">
                            ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span @class([
                            'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                            'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                            'bg-blue-100 text-blue-700'     => in_array($order->status, ['confirmed','preparing']),
                            'bg-purple-100 text-purple-700' => $order->status === 'out_for_delivery',
                            'bg-green-100 text-green-700'   => $order->status === 'delivered',
                            'bg-red-100 text-red-600'       => $order->status === 'cancelled',
                        ])>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        <p class="mt-1 text-sm font-bold text-green-700">₱{{ number_format($order->total, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif


        {{-- ==================== ADMIN DASHBOARD ==================== --}}
        @elseif(auth()->user()->isAdmin())

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Admin Panel</h1>
                <p class="mt-1 text-slate-500">Platform overview and management.</p>
            </div>
            <a href="{{ route('admin.farmers.pending') }}"
               class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                Review Farmers
            </a>
        </div>

        {{-- Stat cards --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('admin.farmers.pending') }}"
               class="rounded-xl {{ $pendingFarmers > 0 ? 'bg-yellow-50 ring-1 ring-yellow-200' : 'bg-white ring-1 ring-slate-200' }} p-6 shadow-sm hover:shadow-md transition">
                <p class="text-3xl">🌾</p>
                <p class="mt-3 text-3xl font-bold {{ $pendingFarmers > 0 ? 'text-yellow-600' : 'text-slate-800' }}">{{ $pendingFarmers }}</p>
                <p class="mt-1 font-semibold text-slate-700">Pending Approvals</p>
                <p class="text-sm text-slate-500">{{ $pendingFarmers > 0 ? 'Action required' : 'All caught up!' }}</p>
            </a>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-3xl">✅</p>
                <p class="mt-3 text-3xl font-bold text-green-700">{{ $totalFarmers }}</p>
                <p class="mt-1 font-semibold text-slate-700">Approved Farmers</p>
                <p class="text-sm text-slate-500">Active on platform</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-3xl">🛒</p>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $totalBuyers }}</p>
                <p class="mt-1 font-semibold text-slate-700">Registered Buyers</p>
                <p class="text-sm text-slate-500">Restaurants, supermarkets</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-3xl">🥦</p>
                <p class="mt-3 text-3xl font-bold text-slate-800">{{ $totalProducts }}</p>
                <p class="mt-1 font-semibold text-slate-700">Listed Products</p>
                <p class="text-sm text-slate-500">Across all farmers</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-3xl">📦</p>
                <p class="mt-3 text-3xl font-bold text-slate-800">{{ $totalOrders }}</p>
                <p class="mt-1 font-semibold text-slate-700">Total Orders</p>
                <p class="text-sm text-slate-500">All time</p>
            </div>

            <div class="rounded-xl bg-green-50 p-6 shadow-sm ring-1 ring-green-200">
                <p class="text-3xl">💰</p>
                <p class="mt-3 text-3xl font-bold text-green-700">₱{{ number_format($totalRevenue, 2) }}</p>
                <p class="mt-1 font-semibold text-slate-700">Platform Revenue</p>
                <p class="text-sm text-slate-500">From paid orders</p>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <a href="{{ route('admin.farmers.pending') }}"
                   class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <span class="text-xl">🌾</span>
                    <div>
                        <p class="font-semibold text-slate-800">Farmer Approvals</p>
                        <p class="text-xs text-slate-500">Review pending applications</p>
                    </div>
                </a>
                <a href="{{ route('marketplace') }}"
                   class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <span class="text-xl">🛒</span>
                    <div>
                        <p class="font-semibold text-slate-800">View Marketplace</p>
                        <p class="text-xs text-slate-500">Browse all listed products</p>
                    </div>
                </a>
                <a href="{{ route('farmers') }}"
                   class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <span class="text-xl">🗺️</span>
                    <div>
                        <p class="font-semibold text-slate-800">Farmer Map</p>
                        <p class="text-xs text-slate-500">See registered farmer locations</p>
                    </div>
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <span class="text-xl">⚙️</span>
                    <div>
                        <p class="font-semibold text-slate-800">Account Settings</p>
                        <p class="text-xs text-slate-500">Update admin profile</p>
                    </div>
                </a>
            </div>
        </div>

        @endif
    </div>
</section>
@endsection