<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\HoldController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentWebhookLogController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Auth Login
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
});

// Products
Route::prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{product}', 'show');
    Route::post('/', 'store');
});

// Holds
Route::prefix('holds')->controller(HoldController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{hold}', 'show');
    Route::post('/', 'store');
});

// Orders
Route::prefix('orders')->controller(OrderController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{order}', 'show');
    Route::post('/', 'store');
});

// Payment Webhook (with idempotency middleware)
Route::prefix('payments/webhook')
    ->controller(PaymentWebhookLogController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{paymentWebhookLog}', 'show');

        // Apply idempotency middleware to the POST endpoint
        Route::post('/', 'process')->middleware('idempotency');
    });
