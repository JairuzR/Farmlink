@extends('layouts.farmlink')

@section('title', 'FARMLINK | Farm Fresh Marketplace')

@php
    $heroImage = 'https://www.figma.com/api/mcp/asset/d33e6fad-7a3e-4c8f-aef8-1bb601ce1ee9';
    $fieldImage = 'https://www.figma.com/api/mcp/asset/2775fbcd-e2b5-468a-9b2e-285d50f6b0c2';
    $carrotImage = 'https://www.figma.com/api/mcp/asset/dad4603d-3de5-4d8e-aa51-e8f072f0983c';
    $marketImage = 'https://www.figma.com/api/mcp/asset/b200b88e-e4ea-44ac-97bd-dc5a656a07a5';

    $farmerProblems = [
        ['Transportation & Logistics', 'Lack of vehicles and cold storage leads to spoiled produce'],
        ['Low & Unstable Prices', 'Market price fluctuations and unfair buyer negotiations'],
        ['Unsold Produce', 'Cancelled orders and low demand result in wasted crops'],
        ['Weather & Crop Damage', 'Typhoons, droughts, and pests destroy harvests'],
        ['Limited Market Access', 'Forced to rely on middlemen without digital platforms'],
        ['Lack of Capital', 'Struggle to afford quality seeds, fertilizers, and equipment'],
    ];

    $consumerProblems = [
        ['Inconsistent Quality', 'Vegetables may not match expected freshness or size'],
        ['Supply Inconsistency', 'Products unavailable due to crop failure or limited harvest'],
        ['Limited Variety', 'Small farms may not produce a wide range of vegetables'],
    ];

    $solutions = [
        ['Direct Market Access', 'Connect directly with buyers without middlemen, maximizing your profits and reducing waste.'],
        ['Fair & Transparent Pricing', 'Set your own prices and negotiate directly. Real-time market insights help you earn more.'],
        ['Secure Transactions', 'Protected payments and buyer commitments reduce the risk of cancelled orders.'],
        ['Quality Assurance', 'Rating system and quality standards ensure customer satisfaction and repeat business.'],
    ];
@endphp

@section('content')
    <section class="bg-gradient-to-br from-green-50 to-emerald-50">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div class="flex flex-col justify-center">
                <h1 class="max-w-2xl text-5xl font-bold leading-none text-slate-950 sm:text-6xl">
                    Connecting <span class="text-green-600">Farmers</span> Directly to Your Table
                </h1>
                <p class="mt-6 max-w-xl text-xl leading-8 text-slate-600">
                    Fresh, quality produce straight from local farms. Support Filipino farmers while getting the best vegetables at fair prices.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('marketplace') }}" class="rounded-lg bg-green-600 px-7 py-4 font-semibold text-white shadow-lg shadow-green-600/20 hover:bg-green-700">Browse Marketplace</a>
                    @guest
                        <a href="{{ route('login') }}" class="rounded-lg bg-green-600 px-7 py-4 font-semibold text-white shadow-lg shadow-green-600/20 hover:bg-green-700">Login</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-slate-800 px-7 py-4 font-semibold text-white shadow-lg shadow-slate-800/20 hover:bg-slate-900">Go to Dashboard</a>
                    @endguest
                </div>
                <div class="mt-10 flex flex-wrap gap-8">
                    <div><strong class="block text-3xl text-green-600">500+</strong><span class="text-slate-600">Active Farmers</span></div>
                    <div><strong class="block text-3xl text-green-600">10k+</strong><span class="text-slate-600">Happy Customers</span></div>
                    <div><strong class="block text-3xl text-green-600">50+</strong><span class="text-slate-600">Varieties</span></div>
                </div>
            </div>
            <img src="{{ $heroImage }}" alt="Fresh produce at a local market" class="h-[500px] w-full rounded-2xl object-cover shadow-2xl">
        </div>
    </section>

    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-4xl font-bold text-slate-950">Problems We're Solving</h2>
                <p class="mt-4 text-xl leading-8 text-slate-600">FARMLINK bridges the gap between farmers and consumers, addressing challenges on both sides of the agricultural supply chain.</p>
            </div>

            <div class="mt-16 grid gap-12 lg:grid-cols-2">
                <div>
                    <h3 class="mb-6 text-2xl font-bold text-green-700">For Farmers</h3>
                    <div class="grid gap-4">
                        @foreach ($farmerProblems as [$title, $body])
                            <div class="flex gap-4 rounded-lg bg-white p-5 shadow-sm">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-green-100 text-green-700">$</span>
                                <div>
                                    <h4 class="font-semibold text-slate-950">{{ $title }}</h4>
                                    <p class="mt-1 text-sm text-slate-600">{{ $body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="mb-6 text-2xl font-bold text-blue-700">For Consumers</h3>
                    <div class="grid gap-4">
                        @foreach ($consumerProblems as [$title, $body])
                            <div class="flex gap-4 rounded-lg bg-white p-5 shadow-sm">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-blue-100 text-blue-700">✓</span>
                                <div>
                                    <h4 class="font-semibold text-slate-950">{{ $title }}</h4>
                                    <p class="mt-1 text-sm text-slate-600">{{ $body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div>
                <h2 class="text-4xl font-bold leading-tight text-slate-950">Empowering Farmers with Digital Tools</h2>
                <p class="mt-5 text-lg leading-8 text-slate-600">FARMLINK provides farmers with the technology and market access they need to thrive in the modern economy.</p>
                <div class="mt-8 grid gap-6">
                    @foreach ($solutions as [$title, $body])
                        <div class="flex gap-4">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-green-100 text-green-700">✓</span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-950">{{ $title }}</h3>
                                <p class="mt-1 leading-7 text-slate-600">{{ $body }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('register', ['role' => 'farmer']) }}" class="mt-8 inline-flex rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">Join as a Farmer</a>
            </div>
            <div class="grid grid-cols-2 gap-4 self-center">
                <img src="{{ $fieldImage }}" alt="A farm field" class="h-64 w-full rounded-xl object-cover shadow-md">
                <img src="{{ $carrotImage }}" alt="Fresh carrots" class="mt-8 h-64 w-full rounded-xl object-cover shadow-md">
                <img src="{{ $marketImage }}" alt="Vegetables in market crates" class="col-span-2 h-64 w-full rounded-xl object-cover shadow-md">
            </div>
        </div>
    </section>

    <section class="bg-green-600 py-20 text-white">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold">Ready to Make a Difference?</h2>
            <p class="mt-4 text-xl text-white/90">Join thousands of Filipinos supporting local farmers and enjoying fresh, quality produce</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('marketplace') }}" class="rounded-lg bg-white px-7 py-4 font-semibold text-green-700 shadow-lg hover:bg-green-50">Start Shopping Now</a>
                <a href="{{ route('register', ['role' => 'user']) }}" class="rounded-lg bg-white px-7 py-4 font-semibold text-green-700 shadow-lg hover:bg-green-50">Register as User</a>
                <a href="{{ route('register', ['role' => 'farmer']) }}" class="rounded-lg border-2 border-white px-7 py-4 font-semibold text-white hover:bg-white/10">Register as Farmer</a>
            </div>
        </div>
    </section>
@endsection
