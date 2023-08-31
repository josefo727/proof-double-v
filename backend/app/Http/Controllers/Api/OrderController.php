<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderCollection;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view-any', Order::class);

        $search = $request->get('search', '');

        $orders = Order::query()
            ->search($search)
            ->latest()
            ->paginate();

        return response()->success(OrderCollection::make($orders));
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $validated = $request->validated();

        // Iniciar una transacción de base de datos para asegurar la integridad de los datos
        DB::beginTransaction();

        try {
            // Crear la orden
            $order = Order::query()->create([
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id()
            ]);

            // Inicializar el total de la orden
            $totalOrder = 0;

            // Crear los ítems asociados a la orden
            foreach ($validated['items'] as $item) {
                $product = Product::query()->find($item['product_id']);
                $quantity = $item['quantity'];
                $price = $product->price; // Asumiendo que tienes un campo de precio en el modelo de Producto
                $subtotal = $price * $quantity;

                // Actualizar el total de la orden
                $totalOrder += $subtotal;

                // Asociar el producto a la orden y guardar la información en la tabla pivote
                $order->products()->attach($item['product_id'], [
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Actualizar el total de la orden
            $order->total = $totalOrder;
            $order->save();

            // Confirmar la transacción
            DB::commit();

            return response()->success(new OrderResource($order), 'Orden creada satisfactoriamente', Response::HTTP_CREATED);

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();
            return response()->error(['message' => 'Se produjo un error intentando registrar la orden'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->success(new OrderResource($order));
    }

    public function update(OrderUpdateRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validated();

        $order->changeStatus($validated['status']);

        return response()->success(new OrderResource($order), 'Estado de la orden actualizado', Response::HTTP_OK);
    }

    public function destroy(Request $request, Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        if ($order->status !== OrderStatus::CANCELLED) {
            return response()->error('No se puede eliminar una orden que no ha sido cancelada', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $order->delete();

        return response()->success(null, null, Response::HTTP_NO_CONTENT);
    }

}
