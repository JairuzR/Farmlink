@extends('layouts.farmlink')

@section('title', 'Marketplace | FARMLINK')

@section('content')
<section class="bg-slate-50 min-h-screen py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center">
            <h1 class="text-4xl font-bold text-slate-950">Marketplace</h1>
            <p class="mt-3 text-slate-600">Fresh produce directly from verified local farmers</p>
        </div>

        {{-- Search + Sort --}}
        <form method="GET" action="{{ route('marketplace') }}" class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search products or descriptions..."
                class="w-72 rounded-lg border border-slate-300 px-4 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
            />
            <select name="sort" class="rounded-lg border border-slate-300 px-4 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="newest"     @selected(request('sort','newest') === 'newest')>Newest</option>
                <option value="price_asc"  @selected(request('sort') === 'price_asc')>Price: Low → High</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High → Low</option>
                <option value="rating"     @selected(request('sort') === 'rating')>Top Rated</option>
            </select>
            <button type="submit" class="rounded-lg bg-green-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
                Search
            </button>
            @if(request('search') || request('sort') || request('category') || request('tag'))
                <a href="{{ route('marketplace') }}" class="text-sm text-slate-500 hover:text-red-500 underline">Clear filters</a>
            @endif
        </form>

        {{-- Category Pills --}}
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
            <a href="{{ route('marketplace', array_merge(request()->except('category'), [])) }}"
               class="{{ !request('category') ? 'bg-green-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }} rounded-full border border-slate-200 px-4 py-1.5 text-sm font-semibold shadow-sm transition">
                All
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('marketplace', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
                   class="{{ request('category') === $cat->slug ? 'bg-green-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50' }} rounded-full border border-slate-200 px-4 py-1.5 text-sm font-semibold shadow-sm transition">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Tag Pills --}}
        @if($tags->isNotEmpty())
        <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
            @foreach ($tags as $tag)
                <a href="{{ route('marketplace', array_merge(request()->except('tag'), ['tag' => $tag->slug])) }}"
                   class="{{ request('tag') === $tag->slug ? 'bg-green-700 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100' }} rounded-full border border-green-200 px-3 py-1 text-xs font-medium transition">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
        @endif

        {{-- Results count --}}
        <p class="mt-6 text-sm text-slate-500 text-center">
            Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
        </p>

        {{-- Product Grid --}}
        <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($products as $product)
                @php
                    $img = $product->primaryImage
                        ? asset('storage/' . $product->primaryImage->path)
                        : 'https://placehold.co/600x400/e2e8f0/94a3b8?text=No+Image';
                    $avg = round($product->reviews_avg_rating ?? $product->averageRating(), 1);
                    $reviewCount = $product->reviews->count();
                @endphp

                <article class="group flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:shadow-md hover:ring-green-300">

                    {{-- Image --}}
                    <a href="{{ route('products.show', $product->slug) }}" class="block overflow-hidden">
                        <img src="{{ $img }}" alt="{{ $product->title }}"
                             class="h-52 w-full object-cover transition duration-300 group-hover:scale-105">
                    </a>

                    <div class="flex flex-1 flex-col p-5">

                        {{-- Category + Status badges --}}
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            @if($product->category)
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700'  => $product->status === 'in_stock',
                                'bg-yellow-100 text-yellow-700'=> $product->status === 'low_stock',
                                'bg-red-100 text-red-600'      => $product->status === 'out_of_stock',
                            ])>
                                {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                            </span>
                            @if(!$product->is_available)
                                <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-500">Unavailable</span>
                            @endif
                        </div>

                        {{-- Title + Farm --}}
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-green-700">
                            <h2 class="text-lg font-bold text-slate-950 leading-snug">{{ $product->title }}</h2>
                        </a>
                        <p class="mt-0.5 text-sm text-slate-500">
                            🌾 {{ $product->farmer->farm_name ?? $product->farmer->name }}
                        </p>

                        {{-- Rating --}}
                        @if($reviewCount > 0)
                        <div class="mt-1.5 flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-3.5 w-3.5 {{ $i <= $avg ? 'text-yellow-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="text-xs text-slate-400 ml-1">{{ $avg }} ({{ $reviewCount }})</span>
                        </div>
                        @else
                            <p class="mt-1.5 text-xs text-slate-400">No reviews yet</p>
                        @endif

                        {{-- Tags --}}
                        @if($product->tags->isNotEmpty())
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($product->tags->take(3) as $tag)
                                <a href="{{ route('marketplace', ['tag' => $tag->slug]) }}"
                                   class="rounded-full bg-green-50 px-2 py-0.5 text-xs text-green-600 hover:bg-green-100">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                            @if($product->tags->count() > 3)
                                <span class="text-xs text-slate-400">+{{ $product->tags->count() - 3 }} more</span>
                            @endif
                        </div>
                        @endif

                        {{-- Harvest date if set --}}
                        @if($product->harvest_date)
                            <p class="mt-2 text-xs text-slate-400">🗓 Harvested: {{ $product->harvest_date->format('M d, Y') }}</p>
                        @endif

                        {{-- Spacer --}}
                        <div class="flex-1"></div>

                        {{-- Price + Cart --}}
                        <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                            <div>
                                <strong class="text-xl text-green-700">₱{{ number_format($product->price, 2) }}</strong>
                                <span class="text-sm text-slate-500"> / {{ $product->unit }}</span>
                                @if($product->minimum_order > 1)
                                    <p class="text-xs text-slate-400">Min. order: {{ $product->minimum_order }}</p>
                                @endif
                            </div>

                            @if($product->is_available && $product->status !== 'out_of_stock')
                                <form method="POST" action="{{ route('cart.store') }}">
                                    @csrf
                                    <input type="hidden" name="product" value="{{ $product->slug }}">
                                    <input type="hidden" name="quantity" value="{{ $product->minimum_order }}">
                                    <button type="submit"
                                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 active:scale-95 transition">
                                        Add to Cart
                                    </button>
                                </form>
                            @else
                                <span class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-400 cursor-not-allowed">
                                    Unavailable
                                </span>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-xl bg-white p-14 text-center shadow-sm ring-1 ring-slate-200">
                    <p class="text-2xl mb-2">🌿</p>
                    <p class="font-semibold text-slate-700">No products found</p>
                    <p class="mt-1 text-sm text-slate-500">Try a different search term, category, or tag.</p>
                    <a href="{{ route('marketplace') }}" class="mt-4 inline-block text-sm text-green-600 hover:underline">Clear all filters</a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="mt-10">
            {{ $products->withQueryString()->links() }}
        </div>
        @endif

    </div>
</section>
@endsection