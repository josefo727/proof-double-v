<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function should_return_paginated_users_with_correct_format(): void
    {
        User::factory()->count(10)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['id', 'name', 'email', 'created_at']
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total']
            ],
            'message',
            'status'
        ]);

        $response->assertJson([
            'message' => 'Datos obtenidos exitosamente',
            'status' => Response::HTTP_OK
        ]);
    }

    /** @test */
    public function should_create_user_for_authenticated_user(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('api.users.store'), $userData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data' => ['id', 'name', 'email']]);
    }

    /** @test */
    public function should_return_error_for_invalid_data(): void
    {
        $userData = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '',
        ];

        $response = $this->postJson(route('api.users.store'), $userData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function should_return_user_details_on_show(): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'message' => 'Datos obtenidos exitosamente',
            'status' => 200
        ]);
    }

    /** @test */
    public function should_return_404_if_user_not_found(): void
    {
        $this->refreshDatabase();

        $nonExistentUserId = 9999;

        $response = $this->getJson("/api/users/{$nonExistentUserId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function should_update_user_and_return_updated_resource(): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create();

        $updatedData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'newpassword123',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /** @test */
    public function should_delete_user_and_return_no_content(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
