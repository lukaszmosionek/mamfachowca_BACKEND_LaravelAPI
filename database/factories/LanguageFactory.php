<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Photo;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->randomElement(['pl','en','es']),
            'name' => $this->faker->unique()->randomElement(['Polish','English','Spanish']),
        ];
    }
}
