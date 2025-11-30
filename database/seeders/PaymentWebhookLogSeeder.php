<?php

namespace Database\Seeders;

use App\Models\PaymentWebhookLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentWebhookLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentWebhookLog::factory()->count(5)->create();
    }
}
