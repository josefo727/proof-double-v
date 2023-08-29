<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderProductsTest extends TestCase
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
    public function it_gets_order_products(): void
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $order->products()->attach($product);

        $response = $this->getJson(route('api.orders.products.index', $order));

        $response->assertOk()->assertSee($product->name);
    }

    /**
     * @test
     */
    public function it_can_attach_products_to_order(): void
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson(
            route('api.orders.products.store', [$order, $product])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $order
                ->products()
                ->where('products.id', $product->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_products_from_order(): void
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $response = $this->deleteJson(
            route('api.orders.products.store', [$order, $product])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $order
                ->products()
                ->where('products.id', $product->id)
                ->exists()
        );
    }
}
