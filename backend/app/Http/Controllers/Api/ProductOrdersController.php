<?php
namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;

class ProductOrdersController extends Controller
{
    public function index(Request $request, Product $product): OrderCollection
    {
        $this->authorize('view', $product);

        $search = $request->get('search', '');

        $orders = $product
            ->orders()
            ->search($search)
            ->latest()
            ->paginate();

        return new OrderCollection($orders);
    }

    public function store(
        Request $request,
        Product $product,
        Order $order
    ): Response {
        $this->authorize('update', $product);

        $product->orders()->syncWithoutDetaching([$order->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        Product $product,
        Order $order
    ): Response {
        $this->authorize('update', $product);

        $product->orders()->detach($order);

        return response()->noContent();
    }
}
