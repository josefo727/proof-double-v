<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductOrdersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_product_orders(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->create();

        $product->orders()->attach($order);

        $response = $this->getJson(
            route('api.products.orders.index', $product)
        );

        $response->assertOk()->assertSee($order->status);
    }

    /**
     * @test
     */
    public function it_can_attach_orders_to_product(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->create();

        $response = $this->postJson(
            route('api.products.orders.store', [$product, $order])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $product
                ->orders()
                ->where('orders.id', $order->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_orders_from_product(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->create();

        $response = $this->deleteJson(
            route('api.products.orders.store', [$product, $order])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $product
                ->orders()
                ->where('orders.id', $order->id)
                ->exists()
        );
    }
}
