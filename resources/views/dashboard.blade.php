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
               class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Product
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
                    @if($avgRating)
                        <span class="inline-flex items-center gap-1">
                            {{ number_format($avgRating, 1) }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </span>
                    @else
                        —
                    @endif
                </strong>
                <p class="mt-2 text-sm text-slate-500">Store Rating</p>
            </div>
            
            {{-- Incoming Orders card --}}
            <a href="{{ route('orders.incoming') }}"
               class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:ring-green-400 hover:bg-green-50 transition group">
                <strong class="text-3xl text-green-700">{{ $activeOrders }}</strong>
                <p class="mt-2 text-sm text-slate-500">Incoming Orders</p>
                <p class="mt-2 text-xs font-semibold text-green-600 group-hover:underline">Manage orders →</p>
            </a>

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
                                    <span class="ml-1 inline-flex items-center gap-0.5 text-xs text-amber-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Low
                                    </span>
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
                <div class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-green-100 text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
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
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-orange-100 text-orange-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                            <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-orange-500">{{ $pendingOrders }}</span>
                </div>
                <p class="mt-3 font-semibold text-slate-800">Pending Orders</p>
                <p class="text-sm text-slate-500 group-hover:text-orange-600">Click to view details</p>
            </a>

            {{-- Delivered orders --}}
            <a href="{{ route('orders.delivered') }}"
               class="rounded-xl bg-green-50 p-5 shadow-sm ring-1 ring-green-100 hover:ring-green-300 transition group">
                <div class="flex items-start justify-between">
                    <div class="grid h-10 w-10 place-items-center rounded-lg bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-slate-700">Edit Profile</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('orders.pending') }}"
                       class="flex items-center justify-between rounded-lg px-4 py-3 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-slate-700">My Orders</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('2fa.setup') }}"
                       class="flex items-center justify-between rounded-lg px-4 py-3 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-slate-700">Two-Factor Auth</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Promo: become a farmer --}}
        <div class="mt-6 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-6 ring-1 ring-green-200">
            <p class="flex items-center gap-2 text-lg font-semibold text-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
                Are you a farmer?
            </p>
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
                <div class="grid h-10 w-10 place-items-center rounded-lg {{ $pendingFarmers > 0 ? 'bg-yellow-100 text-yellow-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="mt-3 text-3xl font-bold {{ $pendingFarmers > 0 ? 'text-yellow-600' : 'text-slate-800' }}">{{ $pendingFarmers }}</p>
                <p class="mt-1 font-semibold text-slate-700">Pending Approvals</p>
                <p class="text-sm text-slate-500">{{ $pendingFarmers > 0 ? 'Action required' : 'All caught up!' }}</p>
            </a>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="mt-3 text-3xl font-bold text-green-700">{{ $totalFarmers }}</p>
                <p class="mt-1 font-semibold text-slate-700">Approved Farmers</p>
                <p class="text-sm text-slate-500">Active on platform</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                    </svg>
                </div>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $totalBuyers }}</p>
                <p class="mt-1 font-semibold text-slate-700">Registered Buyers</p>
                <p class="text-sm text-slate-500">Restaurants, supermarkets</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-slate-100 text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="mt-3 text-3xl font-bold text-slate-800">{{ $totalProducts }}</p>
                <p class="mt-1 font-semibold text-slate-700">Listed Products</p>
                <p class="text-sm text-slate-500">Across all farmers</p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-slate-100 text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="mt-3 text-3xl font-bold text-slate-800">{{ $totalOrders }}</p>
                <p class="mt-1 font-semibold text-slate-700">Total Orders</p>
                <p class="text-sm text-slate-500">All time</p>
            </div>

            <div class="rounded-xl bg-green-50 p-6 shadow-sm ring-1 ring-green-200">
                <div class="grid h-10 w-10 place-items-center rounded-lg bg-green-100 text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                    </svg>
                </div>
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
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-yellow-100 text-yellow-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Farmer Approvals</p>
                        <p class="text-xs text-slate-500">Review pending applications</p>
                    </div>
                </a>
                <a href="{{ route('marketplace') }}"
                   class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">View Marketplace</p>
                        <p class="text-xs text-slate-500">Browse all listed products</p>
                    </div>
                </a>
                <a href="{{ route('farmers') }}"
                   class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zm10.586 13.414L18 14.586V4a1 1 0 00-1.707-.707L14 5.586v11.121z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Farmer Map</p>
                        <p class="text-xs text-slate-500">See registered farmer locations</p>
                    </div>
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3 hover:bg-slate-50 transition">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-slate-100 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                    </div>
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