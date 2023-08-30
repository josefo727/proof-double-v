<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view-any', Product::class);

        $search = $request->get('search', '');

        $products = Product::query()
            ->search($search)
            ->latest()
            ->paginate();

        return response()->success(ProductCollection::make($products));
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $validated = $request->validated();

        $product = Product::query()->create($validated);

        return response()->success(new ProductResource($product), 'Record successfully created', Response::HTTP_CREATED);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return response()->success(new ProductResource($product));
    }

    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validated();

        $product->update($validated);

        return response()->success(new ProductResource($product), 'Record successfully updated', Response::HTTP_OK);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->success(null, null, Response::HTTP_NO_CONTENT);
    }
}
