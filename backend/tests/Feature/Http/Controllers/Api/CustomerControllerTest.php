<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\User;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function should_return_paginated_customers_with_correct_format(): void
    {
        // Create some clients using the factory
        Customer::factory()->count(10)->create();

        // Perform GET request to the endpoint
        $response = $this->getJson('/api/customers');

        // Ensure that the response has a status of 200
        $response->assertStatus(Response::HTTP_OK);

        // Verify that the response has the expected structure
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['id', 'name', 'email', 'phone', 'created_at']
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total']
            ],
            'message',
            'status'
        ]);

        // Verify that the message and status are as expected
        $response->assertJson([
            'message' => 'Datos obtenidos exitosamente',
            'status' => Response::HTTP_OK
        ]);
    }

    /** @test */
    public function should_create_customer_for_authenticated_user(): void
    {
        $customerData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson(route('api.customers.store'), $customerData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'phone']]);
    }

    /** @test */
    public function should_return_error_for_invalid_data(): void
    {
        $customerData = [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => '',
        ];

        $response = $this->postJson(route('api.customers.store'), $customerData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);  // Unprocessable Entity
    }

    /** @test */
    public function should_return_customer_details_on_show(): void
    {
        // Use the RefreshDatabase trait to reset the database
        $this->refreshDatabase();

        // Create a client for the test
        $customer = Customer::factory()->create();

        // Perform GET request to display customer details
        $response = $this->getJson("/api/customers/{$customer->id}");

        // Ensure that the response has a status of 200 OK.
        $response->assertStatus(Response::HTTP_OK);

        // Ensure that the response includes the customer's details
        $response->assertJson([
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            "message" => "Datos obtenidos exitosamente",
            "status" => 200
        ]);
    }

    /** @test */
    public function should_return_404_if_customer_not_found(): void
    {
        // Use the RefreshDatabase trait to reset the database
        $this->refreshDatabase();

        // ID of a customer that does not exist
        $nonExistentCustomerId = 9999;

        // Perform GET request to display customer details
        $response = $this->getJson("/api/customers/{$nonExistentCustomerId}");

        // Ensure that the response has a 404 Not Found status
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function should_update_customer_and_return_updated_resource(): void
    {
        // Use the RefreshDatabase trait to reset the database
        $this->refreshDatabase();

        // Create a client for testing
        $customer = Customer::factory()->create();

        // Updated customer data
        $updatedData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
        ];

        // Perform PUT request to update the client
        $response = $this->putJson("/api/customers/{$customer->id}", $updatedData);

        // Ensure that the response has a status of 200 OK
        $response->assertStatus(Response::HTTP_OK);

        // Verify that customer data has been correctly updated
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
            'phone' => $updatedData['phone'],
        ]);
    }

    /** @test */
    public function should_delete_customer_and_return_no_content(): void
    {
        // Create a client for testing
        $customer = Customer::factory()->create();

        // Perform DELETE request
        $response = $this->deleteJson("/api/customers/{$customer->id}");

        // Ensure that the customer has been removed and that the response is 204
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        // Verify that the customer no longer exists in the database
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}

