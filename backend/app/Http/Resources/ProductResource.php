<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            "id" =>  $this->id,
            "sku" =>  $this->sku,
            "name" =>  $this->name,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "created_by" => $this->creator?->name,
            "created_at" =>  $this->created_at->diffForHumans(),
        ];
    }
}
