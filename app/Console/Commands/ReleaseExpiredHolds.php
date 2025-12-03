<?php

namespace App\Console\Commands;

use App\Models\Hold;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredHolds extends Command
{
    protected $signature = 'holds:release-expired';
    protected $description = 'Release stock from expired holds';

    public function handle()
    {
        $expiredHolds = Hold::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredHolds as $hold) {
            try {
                DB::transaction(function () use ($hold) {
                    $product = Product::lockForUpdate()->find($hold->product_id);
                    $product->increment('stock', $hold->quantity);

                    $hold->update(['status' => 'expired']);

                    Cache::forget("product_stock_{$product->id}");

                    Log::info("Released hold {$hold->id}, returned {$hold->quantity} stock");
                });
            } catch (\Exception $e) {
                Log::error("Failed to release hold {$hold->id}: {$e->getMessage()}");
            }
        }

        $this->info("Released {$expiredHolds->count()} expired holds");
    }
}
