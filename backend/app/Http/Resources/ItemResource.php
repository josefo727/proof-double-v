<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->pivot->price,
            'quantity' => $this->pivot->quantity,
            'subtotal' => $this->pivot->subtotal,
        ];
    }
}
