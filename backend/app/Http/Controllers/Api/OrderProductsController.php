<?php
namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;

class OrderProductsController extends Controller
{
    public function index(Request $request, Order $order): ProductCollection
    {
        $this->authorize('view', $order);

        $search = $request->get('search', '');

        $products = $order
            ->products()
            ->search($search)
            ->latest()
            ->paginate();

        return new ProductCollection($products);
    }

    public function store(
        Request $request,
        Order $order,
        Product $product
    ): Response {
        $this->authorize('update', $order);

        $order->products()->syncWithoutDetaching([$product->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        Order $order,
        Product $product
    ): Response {
        $this->authorize('update', $order);

        $order->products()->detach($product);

        return response()->noContent();
    }
}
