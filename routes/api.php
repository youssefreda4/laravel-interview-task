<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\HoldController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
});

Route::get('products', [ProductController::class, 'index']);


Route::prefix('holds')->controller(HoldController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
});

Route::prefix('orders')->controller(OrderController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
});
