<?php

namespace App\Providers;

use App\Models\PaymentWebhookLog;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('paymentWebhookLog', function ($value) {
            return PaymentWebhookLog::where('idempotency_key', $value)->firstOrFail();
        });
    }
}
