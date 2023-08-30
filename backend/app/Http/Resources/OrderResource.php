<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            "id" =>  $this->id,
            "total" =>  round($this->total, 2),
            "status" => $this->status,
            "status_name" =>  $this->state->getLabel(),
            'customer' => new CustomerResource($this->customer),
            "items" => new ItemCollection($this->products),
            "created_at" =>  $this->created_at->diffForHumans(),
            "created_by" => $this->creator->name
        ];
    }
}
