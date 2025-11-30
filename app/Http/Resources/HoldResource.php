<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HoldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'product_id'  => $this->product_id,
            'quantity'    => $this->quantity,
            'status'      => enum_data($this->status),
            'expires_at'  => $this->expires_at?->format('Y-m-d H:i:s'),
            'created_at'  => $this->created_at?->diffForHumans(),
        ];
    }
}
