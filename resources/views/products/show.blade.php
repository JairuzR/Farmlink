@extends('layouts.farmlink')

@section('title', $product->title . ' | FARMLINK')

@section('content')
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_420px]">

            {{-- Left: Images + Description --}}
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @php
                    $primaryImg = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    $imgUrl = $primaryImg
                        ? asset('storage/' . $primaryImg->path)
                        : 'https://placehold.co/800x600/e2e8f0/94a3b8?text=No+Image';
                @endphp
                <img src="{{ $imgUrl }}" alt="{{ $product->title }}"
                     class="h-[420px] w-full rounded-lg object-cover" id="main-img">

                {{-- Thumbnail strip --}}
                @if($product->images->count() > 1)
                <div class="mt-3 flex gap-2 overflow-x-auto">
                    @foreach($product->images as $img)
                        <img src="{{ asset('storage/' . $img->path) }}"
                             alt=""
                             onclick="document.getElementById('main-img').src=this.src"
                             class="h-20 w-20 flex-shrink-0 cursor-pointer rounded-lg object-cover ring-2 ring-transparent hover:ring-green-500">
                    @endforeach
                </div>
                @endif

                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-slate-950">About this Produce</h2>
                    <p class="mt-3 leading-8 text-slate-600">{{ $product->description }}</p>

                    {{-- Tags --}}
                    @if($product->tags->isNotEmpty())
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($product->tags as $tag)
                            <a href="{{ route('marketplace', ['tag' => $tag->slug]) }}"
                               class="rounded-full bg-green-50 px-3 py-1 text-sm text-green-700 hover:bg-green-100">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Reviews section --}}
                <div class="mt-10 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-bold text-slate-950">Reviews</h2>

                    @if($product->reviews->isEmpty())
                        <p class="mt-4 text-slate-500">No reviews yet. Be the first!</p>
                    @else
                        @foreach($product->reviews as $review)
                        <div class="mt-5 rounded-lg bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-800">{{ $review->user->name }}</span>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-slate-600">{{ $review->comment }}</p>
                        </div>
                        @endforeach
                    @endif

                    {{-- Leave a review (buyers only, must be logged in) --}}
                    @auth
                        @if(auth()->user()->isBuyer())
                        <div class="mt-8 rounded-lg border border-slate-200 p-5">
                            <h3 class="font-semibold text-slate-800">Leave a Review</h3>
                            <form method="POST" action="{{ route('reviews.store', $product->slug) }}" class="mt-3 space-y-3">
                                @csrf
                                <div>
                                    <label class="text-sm text-slate-600">Rating</label>
                                    <div class="mt-1 flex gap-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $i }}" class="sr-only" required>
                                                <svg class="h-7 w-7 text-slate-300 hover:text-yellow-400 peer-checked:text-yellow-400"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm text-slate-600">Comment (optional)</label>
                                    <textarea name="comment" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                        placeholder="Share your experience..."></textarea>
                                </div>
                                <button type="submit"
                                    class="rounded-lg bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                    Submit Review
                                </button>
                            </form>
                        </div>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Right: Sidebar --}}
            <aside class="self-start rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sticky top-6">
                {{-- Status badge --}}
                <div class="flex flex-wrap gap-2">
                    <span @class([
                        'rounded-full px-3 py-1 text-sm font-semibold',
                        'bg-green-100 text-green-700'  => $product->status === 'in_stock',
                        'bg-yellow-100 text-yellow-700'=> $product->status === 'low_stock',
                        'bg-red-100 text-red-600'      => $product->status === 'out_of_stock',
                    ])>{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
                    @if(!$product->is_available)
                        <span class="rounded-full bg-slate-200 px-3 py-1 text-sm font-semibold text-slate-500">Unavailable</span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-bold text-slate-950">{{ $product->title }}</h1>
                @if($product->category)
                    <p class="mt-1 text-sm text-slate-500">{{ $product->category->name }}</p>
                @endif

                <p class="mt-5 text-3xl font-bold text-green-700">
                    ₱{{ number_format($product->price, 2) }}
                    <span class="text-base font-normal text-slate-500">/ {{ $product->unit }}</span>
                </p>

                <dl class="mt-5 grid gap-2 text-sm">
                    <div class="flex justify-between rounded-lg bg-slate-50 p-3">
                        <dt class="text-slate-500">Minimum order</dt>
                        <dd class="font-semibold">{{ $product->minimum_order }} {{ $product->unit }}</dd>
                    </div>
                    <div class="flex justify-between rounded-lg bg-slate-50 p-3">
                        <dt class="text-slate-500">Available stock</dt>
                        <dd class="font-semibold">{{ $product->stock }} {{ $product->unit }}</dd>
                    </div>
                    @if($product->pickup_location)
                    <div class="flex justify-between rounded-lg bg-slate-50 p-3">
                        <dt class="text-slate-500">Pickup area</dt>
                        <dd class="font-semibold">{{ $product->pickup_location }}</dd>
                    </div>
                    @endif
                    @if($product->harvest_date)
                    <div class="flex justify-between rounded-lg bg-slate-50 p-3">
                        <dt class="text-slate-500">Harvested</dt>
                        <dd class="font-semibold">{{ $product->harvest_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>

                {{-- Add to cart --}}
                @if($product->is_available && $product->status !== 'out_of_stock')
                <form method="POST" action="{{ route('cart.store') }}" class="mt-5 flex gap-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="number" name="quantity"
                           value="{{ $product->minimum_order }}"
                           min="{{ $product->minimum_order }}"
                           max="{{ $product->stock }}"
                           class="w-28 rounded-lg border-slate-300 text-center"
                           aria-label="Quantity">
                    <button type="submit"
                        class="flex-1 rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700">
                        Add to Cart
                    </button>
                </form>
                @else
                    <div class="mt-5 rounded-lg bg-slate-100 px-5 py-3 text-center font-semibold text-slate-400">
                        Currently Unavailable
                    </div>
                @endif

                {{-- Farmer card --}}
                <div class="mt-6 rounded-lg border border-slate-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Farmer</p>
                    <p class="font-bold text-slate-900">{{ $product->farmer->name }}</p>
                    @if($product->farmer->farm_name)
                        <p class="text-sm text-slate-500">🌾 {{ $product->farmer->farm_name }}</p>
                    @endif
                    @if($product->farmer->phone)
                        <p class="mt-1 text-sm text-slate-600">📞 {{ $product->farmer->phone }}</p>
                    @endif
                    @if($product->farmer->bio)
                        <p class="mt-2 text-sm text-slate-500 italic">{{ $product->farmer->bio }}</p>
                    @endif

                    {{-- Social links --}}
                    @if($product->farmer->socialLinks->isNotEmpty())
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($product->farmer->socialLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200">
                                {{ $link->label ?? $link->platform }}
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Farmer's other products --}}
                @php
                    $otherProducts = $product->farmer->products()
                        ->where('id', '!=', $product->id)
                        ->available()
                        ->with('primaryImage')
                        ->latest()->take(3)->get();
                @endphp
                @if($otherProducts->isNotEmpty())
                <div class="mt-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">More from this farmer</p>
                    <div class="space-y-2">
                        @foreach($otherProducts as $other)
                        <a href="{{ route('products.show', $other->slug) }}"
                           class="flex items-center gap-3 rounded-lg p-2 hover:bg-slate-50">
                            @php $thumb = $other->primaryImage ? asset('storage/'.$other->primaryImage->path) : 'https://placehold.co/80x80/e2e8f0/94a3b8?text=?'; @endphp
                            <img src="{{ $thumb }}" class="h-12 w-12 rounded-lg object-cover flex-shrink-0">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $other->title }}</p>
                                <p class="text-xs text-green-700">₱{{ number_format($other->price, 2) }} / {{ $other->unit }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>
</section>
@endsection