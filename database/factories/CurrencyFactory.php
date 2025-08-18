<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Photo;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->randomElement(['USD','PLN','EUR']),
            'symbol' => $this->faker->unique()->randomElement(['$','zł','€']),
            'rate' => $this->faker->randomFloat(4, 0.5, 5), // np. kurs od 0.5 do 5
            'language_id' => $attributes['language_id'] ?? Language::factory(),
        ];

    }
}
