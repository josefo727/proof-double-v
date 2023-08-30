<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function should_return_token_on_valid_login(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);
    }

    /** @test */
    public function should_return_error_on_invalid_login(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => ['message' => 'Credenciales no válidas']]);
    }

    /** @test */
    public function should_return_server_error_on_exception(): void
    {
        // Simulate an exception in the controller
        $controller = new LoginController();
        $mock = $this->getMockBuilder(LoginController::class)
            ->onlyMethods(['__invoke'])
            ->getMock();
        $mock->method('__invoke')
            ->willThrowException(new \Exception('Simulated server error'));

        // Ensuring that an exception is thrown
        $this->expectException(\Exception::class);

        try {
            $response = $mock->__invoke(new LoginRequest([
                'email' => 'test@example.com',
                'password' => 'password',
            ]));
        } catch (\Exception $e) {
            // Capture the exception and make the necessary assertions
            $this->assertEquals('Simulated server error', $e->getMessage());
            $this->assertEquals(500, $response->getStatusCode());
        }
    }
}
