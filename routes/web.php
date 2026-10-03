<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

// Storefront (Inertia + React). Cache-friendly GETs; thin controllers.
Route::get('/', [ShopController::class, 'home'])->name('home');

// Category-driven shop (admin creates categories; slug drives the flow).
// stickers + wallpaper + coloring-books => details page + instant download.
// video + egift-card => details page + multi-step wizard.
Route::get('/shop/{category}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/shop/{category}/{product}', [ShopController::class, 'product'])->name('shop.product');
Route::get('/order/{product}', [ShopController::class, 'wizard'])->name('order.wizard');

// Legacy URLs (reference project) → same category pages.
Route::get('/stickers', [ShopController::class, 'stickers'])->name('stickers');
Route::get('/custom-videos', [ShopController::class, 'customVideos'])->name('custom-videos');
Route::get('/coloring-books', [ShopController::class, 'coloringBooks'])->name('coloring-books');

Route::get('/about', [ShopController::class, 'about'])->name('about');
Route::get('/contact', [ShopController::class, 'contact'])->name('contact');
Route::get('/track', [TrackController::class, 'show'])->name('track')->middleware('throttle:30,1');
Route::get('/checkout/success', [ShopController::class, 'checkoutSuccess'])->name('checkout.success');

Route::post('/checkout', [ShopController::class, 'checkout'])->name('checkout.create')->middleware('throttle:30,1');
Route::post('/video-orders', [ShopController::class, 'storeVideoOrder'])->name('video-orders.store')->middleware('throttle:30,1');
Route::post('/contact', [ShopController::class, 'storeContact'])->name('contact.store');

// Auth (Inertia + session).
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    // Google one-tap login + registration.
    Route::get('/auth/google', [SocialAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');
});

// Customer panel (requires login).
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account');
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/downloads', [AccountController::class, 'downloads'])->name('account.downloads');
    Route::post('/account/reviews', [AccountController::class, 'storeReview'])->name('account.reviews.store');
});

// Secure deliverable download (verifies paid order, then signs R2 URL).
Route::post('/download/{product}', [ShopController::class, 'download'])->name('download.product')->middleware('throttle:30,1');

// Lemon Squeezy webhooks are handled by the lemonsqueezy/laravel package
// (POST lemon-squeezy/webhook → signature-verified WebhookController →
// OrderCreated event → our RecordShopOrder listener). Do NOT define a route
// here — it would shadow the package route and silently drop webhooks.
