<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
Route::view('/farmers', 'farmers')->name('farmers');
Route::get('/cart', [CartController::class, 'show'])->name('cart');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
Route::get('/orders/delivered', [OrderController::class, 'delivered'])->name('orders.delivered');
Route::patch('/orders/{order}/delivered', [OrderController::class, 'markDelivered'])->name('orders.mark-delivered');
Route::get('/products/create', [MarketplaceController::class, 'create'])->middleware(['auth', 'verified'])->name('products.create');
Route::post('/products', [MarketplaceController::class, 'store'])->middleware(['auth', 'verified'])->name('products.store');
Route::get('/products/{product}', [MarketplaceController::class, 'show'])->name('products.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
