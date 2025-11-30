<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLog extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentWebhookLogFactory> */
    use HasFactory;

    protected $fillable = [
        'idempotency_key',
        'payload',
        'order_id',
        'processed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
