<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
			'data' => UserResource::collection($this->collection),
			...(new PaginationResource($this))->toArray($request)
		];
    }
}
