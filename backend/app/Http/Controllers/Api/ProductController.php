<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{
    public function index(Request $request): ProductCollection
    {
        $this->authorize('view-any', Product::class);

        $search = $request->get('search', '');

        $products = Product::search($search)
            ->latest()
            ->paginate();

        return new ProductCollection($products);
    }

    public function store(ProductStoreRequest $request): ProductResource
    {
        $this->authorize('create', Product::class);

        $validated = $request->validated();

        $product = Product::create($validated);

        return new ProductResource($product);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return new ProductResource($product);
    }

    public function update(
        ProductUpdateRequest $request,
        Product $product
    ): ProductResource {
        $this->authorize('update', $product);

        $validated = $request->validated();

        $product->update($validated);

        return new ProductResource($product);
    }

    public function destroy(Request $request, Product $product): Response
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->noContent();
    }
}
