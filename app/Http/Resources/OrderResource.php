<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'product_id'   => $this->whenLoaded('product', fn () => new ProductResource($this->product)),
            'hold_id'      => $this->whenLoaded('hold', fn () => new HoldResource($this->hold)),
            'quantity'     => $this->quantity,
            'price'        => $this->price,
            'total_price'  => $this->total_price,
            'status'       => enum_data($this->status),
            'created_at'   => $this->created_at?->diffForHumans(),
        ];
    }
}
