<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Services\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_change_order_status(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::CREATED]);

        $newStatus = OrderStatus::ACCEPTED;
        $order->changeStatus($newStatus);

        $this->assertEquals(OrderStatus::ACCEPTED, $order->fresh()->status);
    }

    /** @test */
    public function should_not_change_to_invalid_status(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::CREATED]);

        $newStatus = OrderStatus::DELIVERED;
        $order->changeStatus($newStatus);

        $this->assertEquals(OrderStatus::CREATED, $order->fresh()->status);
    }
}
