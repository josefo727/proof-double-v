<?php

namespace Database\Factories;

use App\Models\Order;
use App\Services\OrderStatus;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total' => $this->faker->randomFloat(2, 0, 9999),
            'status' => Arr::random(OrderStatus::getAll()),
            'customer_id' => \App\Models\Customer::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
