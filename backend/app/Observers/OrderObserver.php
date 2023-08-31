<?php

namespace App\Observers;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Services\OrderStatus;

class OrderObserver
{
    public function created(Order $order): void
    {
        event(new OrderCreated($order));
    }

    public function updated(Order $order): void
    {
        if ($order->getOriginal('status') !== $order->status && $order->status === OrderStatus::CANCELLED) {
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                $product->increment('quantity', $quantity);
            }
        }

        if ($order->isDirty('status')) {
            event(new OrderStatusChanged($order));
        }
    }
}
