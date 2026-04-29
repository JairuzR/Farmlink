@extends('layouts.farmlink')

@php($isAdmin = Auth::user()?->role === 'admin')

@section('title', ($isAdmin ? 'Admin Dashboard' : 'Farmer Dashboard').' | FARMLINK')

@section('content')
    <section class="bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-4xl font-bold text-slate-950">{{ $isAdmin ? 'Admin Dashboard' : 'Farmer Dashboard' }}</h1>
                    <p class="mt-2 text-slate-600">Welcome back, {{ Auth::user()->name ?? 'Farmer' }}.</p>
                </div>
                <a href="{{ route('products.create') }}" class="rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">Add Product</a>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['₱38,500', 'Monthly Revenue'], ['245 kg', 'Produce Sold'], ['23', 'Active Orders'], ['4.9', 'Store Rating']] as [$value, $label])
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <strong class="text-3xl text-green-700">{{ $value }}</strong>
                        <p class="mt-2 text-sm text-slate-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-xl font-bold text-slate-950">My Products</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-slate-200 text-slate-500">
                                <tr><th class="py-3">Product</th><th>Stock</th><th>Price</th><th>Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="py-4 font-semibold">Fresh Tomatoes</td><td>120 kg</td><td>₱120/kg</td><td class="text-green-700">Active</td></tr>
                                <tr><td class="py-4 font-semibold">Organic Cabbage</td><td>80 kg</td><td>₱85/kg</td><td class="text-green-700">Active</td></tr>
                                <tr><td class="py-4 font-semibold">Red Bell Peppers</td><td>18 kg</td><td>₱180/kg</td><td class="text-amber-700">Low Stock</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-xl font-bold text-slate-950">Sales Overview</h2>
                    <div class="mt-6 grid gap-4">
                        <div class="flex justify-between rounded-lg bg-green-50 p-4"><span>Today</span><strong class="text-green-700">₱2,450</strong></div>
                        <div class="flex justify-between rounded-lg bg-blue-50 p-4"><span>This Week</span><strong class="text-blue-700">₱18,200</strong></div>
                        <div class="flex justify-between rounded-lg bg-violet-50 p-4"><span>This Month</span><strong class="text-violet-700">₱38,500</strong></div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
