<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['farmer', 'primaryImage', 'category', 'tags', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->available();

        // Search
        if ($search = $request->query('search')) {
            $query->search($search);
        }

        // Filter by category slug
        if ($category = $request->query('category')) {
            $query->byCategory($category);
        }

        // Filter by tag slug
        if ($tag = $request->query('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $tag));
        }

        // Sort
        match ($request->query('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::whereNull('user_id')->orderBy('name')->get();

        return view('marketplace', compact('products', 'categories', 'tags'));
    }

    public function farmers(): \Illuminate\View\View
    {
        $farmers = \App\Models\User::where('role', 'farmer')
            ->where('is_approved', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount(['products' => fn($q) => $q->where('is_available', true)])
            ->with('socialLinks')
            ->get(['id', 'name', 'farm_name', 'latitude', 'longitude', 'bio', 'phone']);

        $farmersJson = $farmers->map(fn($f) => [
            'id'        => $f->id,
            'name'      => $f->name,
            'farm_name' => $f->farm_name,
            'lat'       => (float) $f->latitude,
            'lng'       => (float) $f->longitude,
            'products'  => $f->products_count,
            'rating'    => null, // add avg rating later if needed
            'url'       => route('marketplace', ['search' => $f->farm_name ?? $f->name]),
        ])->values();

        return view('farmers', compact('farmers', 'farmersJson'));
    }
}