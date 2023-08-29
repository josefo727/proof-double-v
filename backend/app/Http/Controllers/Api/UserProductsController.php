<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class UserProductsController extends Controller
{
    public function index(Request $request, User $user): ProductCollection
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $products = $user
            ->products()
            ->search($search)
            ->latest()
            ->paginate();

        return new ProductCollection($products);
    }

    public function store(Request $request, User $user): ProductResource
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'sku' => ['required', 'unique:products,sku', 'max:50', 'string'],
            'name' => ['required', 'max:50', 'string'],
            'price' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
        ]);

        $product = $user->products()->create($validated);

        return new ProductResource($product);
    }
}
