<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceTranslation>
 */
class ServiceTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $language = Language::inRandomOrder()->first();

        return [
            'name' => $this->faker->sentence(10).' '.$language->code,
            'description' => $this->faker->sentence(120),
            'language_id' => $language->id
        ];
    }
}
