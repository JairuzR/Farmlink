<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // One review per buyer per product
        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $product->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return back()->with('status', 'Review submitted!');
    }
}