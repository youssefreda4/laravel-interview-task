<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentWebhookLog>
 */
class PaymentWebhookLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idempotency_key' => Str::uuid(),
            'payload' => $this->faker->randomElements(['status' => 'success', 'order_id' => rand(1, 10)], 1),
            'order_id' => Order::factory(),
            'processed_at' => now(),
        ];
    }
}
