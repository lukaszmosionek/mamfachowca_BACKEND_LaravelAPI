<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Photo;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'provider_id' => fn(array $attributes) => $attributes['provider_id'] ?? User::factory(),
            'price' => $this->faker->randomFloat(0, 50, 300),
            'duration_minutes' => $this->faker->numberBetween(15, 90),
            'currency_id' => Currency::inRandomOrder()->first()->id,
        ];

    }
}
