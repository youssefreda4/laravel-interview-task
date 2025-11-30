<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentWebhookLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'idempotency_key'  => $this->idempotency_key,
            'order_id'         => $this->order_id,
            'payload'          => $this->payload,
            'processed_at'     => $this->processed_at?->diffForHumans(),
            'created_at'       => $this->created_at?->diffForHumans(),
        ];
    }
}
