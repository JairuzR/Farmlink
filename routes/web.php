<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\ProductController;
use App\Models\User;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/farmers/pending', [AdminController::class, 'pendingFarmers'])->name('farmers.pending');
    Route::patch('/farmers/{user}/approve', [AdminController::class, 'approveFarmer'])->name('farmers.approve');
    Route::delete('/farmers/{user}/reject', [AdminController::class, 'rejectFarmer'])->name('farmers.reject');
    Route::get('/farmers/{user}/id', function (App\Models\User $user) {
        abort_unless($user->farmer_id_path, 404);
        return response()->file(storage_path('app/private/' . $user->farmer_id_path));
    })->name('farmers.id');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::delete('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/challenge', [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
});

Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware(['auth', 'verified', 'role:farmer', 'approved'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product:slug}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product:slug}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product:slug}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product:slug}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
});

require __DIR__.'/auth.php';
