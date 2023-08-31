<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function should_return_a_list_of_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'total',
                            'status',
                            'status_name',
                            'customer' => [
                                'id',
                                'name',
                                'email',
                                'phone',
                                'created_at'
                            ],
                            'items' => [
                                '*' => [
                                    'sku',
                                    'name',
                                    'price',
                                    'quantity',
                                    'subtotal'
                                ]
                            ],
                            'created_at',
                            'created_by'
                        ]
                    ],
                    'links' => [
                        'first',
                        'last',
                        'prev',
                        'next'
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'last_page',
                        'path',
                        'per_page',
                        'to',
                        'total'
                    ]
                ],
                'message',
                'status'
            ]);
    }

    /** @test */
    public function should_creates_an_order_and_associated_items(): void
    {
        $customer = Customer::factory()->create();
        $products = Product::factory()->count(2)->create();
        $payload = [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $products[0]->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $products[1]->id,
                    'quantity' => 3,
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'total',
                    'status',
                    'status_name',
                    'customer' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'created_at'
                    ],
                    'items' => [
                        '*' => [
                            'sku',
                            'name',
                            'price',
                            'quantity',
                            'subtotal'
                        ]
                    ],
                    'created_at',
                    'created_by'
                ],
                'message',
                'status'
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $response['data']['id'],
            'customer_id' => $customer->id,
            'total' => $response['data']['total'],
        ]);

        foreach ($response['data']['items'] as $item) {
            $this->assertDatabaseHas('order_product', [
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }
    }

    /** @test */
    public function should_return_order_details(): void
    {
        $order = Order::factory()->create(); // Asegúrate de que la fábrica de órdenes también crea items y clientes asociados

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'total',
                    'status',
                    'status_name',
                    'customer' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'created_at'
                    ],
                    'items' => [
                        '*' => [
                            'sku',
                            'name',
                            'price',
                            'quantity',
                            'subtotal'
                        ]
                    ],
                    'created_at',
                    'created_by'
                ],
                'message',
                'status'
            ]);
    }


    /** @test */
    public function should_update_order_status(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::CREATED
        ]); // Asegúrate de que la fábrica de órdenes también crea items y clientes asociados

        $newStatus = OrderStatus::ACCEPTED;
        $payload = ['status' => $newStatus];

        $response = $this->putJson("/api/orders/{$order->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'total',
                    'status',
                    'status_name',
                    'customer' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'created_at'
                    ],
                    'items' => [
                        '*' => [
                            'sku',
                            'name',
                            'price',
                            'quantity',
                            'subtotal'
                        ]
                    ],
                    'created_at',
                    'created_by'
                ],
                'message',
                'status'
            ])
            ->assertJson([
                'data' => [
                    'status' => $newStatus
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => $newStatus
        ]);
    }

    /** @test */
    public function should_delete_a_cancelled_order(): void
    {
        $order = Order::factory()->create();

        $order->changeStatus(OrderStatus::CANCELLED);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    /** @test */
    public function should_not_delete_a_non_cancelled_order(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::CREATED
        ]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'error' => [

                    'message' => 'No se puede eliminar una orden que no ha sido cancelada',
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CREATED
        ]);
    }
}
