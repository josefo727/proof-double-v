<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function should_return_paginated_products_with_correct_format(): void
    {
        Product::factory()->count(10)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'sku', 'name', 'price', 'quantity', 'created_by', 'created_at']
                    ],
                    'links' => ['first', 'last', 'prev', 'next'],
                    'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total']
                ]
            ]);
    }

    /** @test */
    public function should_create_product_for_authenticated_user(): void
    {
        $productData = [
            'sku' => 'SKU123',
            'name' => 'Product Name',
            'price' => 100.50,
            'quantity' => 10,
        ];

        $response = $this->postJson(route('api.products.store'), $productData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data' => ['id', 'sku', 'name', 'price', 'quantity', 'created_by', 'created_at']]);
    }

    /** @test */
    public function should_return_error_for_invalid_data(): void
    {
        $productData = [
            'sku' => '',
            'name' => '',
            'price' => 'not-a-number',
            'quantity' => 'not-a-number',
        ];

        $response = $this->postJson(route('api.products.store'), $productData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function should_return_product_details_on_show(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                ]
            ]);
    }

    /** @test */
    public function should_return_404_if_product_not_found(): void
    {
        $nonExistentProductId = 9999;

        $response = $this->getJson("/api/products/{$nonExistentProductId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function should_update_product_and_return_updated_resource(): void
    {
        $product = Product::factory()->create();

        $updatedData = [
            'sku' => 'NEW-SKU123',
            'name' => 'New Product Name',
            'price' => 200.50,
            'quantity' => 20,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updatedData);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sku' => $updatedData['sku'],
            'name' => $updatedData['name'],
            'price' => $updatedData['price'],
            'quantity' => $updatedData['quantity'],
        ]);
    }

    /** @test */
    public function should_delete_product_and_return_no_content(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
