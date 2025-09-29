<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Photo;
use App\Models\Service;
use App\Models\ServiceTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $currency = Currency::inRandomOrder()->first();

        return [
            'provider_id' => $attributes['provider_id'] ?? User::factory(),
            'price' => $this->faker->randomFloat(0, 50, 300),
            'duration_minutes' => $this->faker->numberBetween(15, 90),
            'currency_id' => $currency ? $currency->id : Currency::factory(),
        ];

    }

    public function withTranslations(array $attributes = [])
    {
        return $this->afterCreating(function ($service) use ($attributes) {
            $languages = \App\Models\Language::all();

            foreach ($languages as $language) {
                \App\Models\ServiceTranslation::factory()->create(array_merge($attributes, [
                    'service_id'   => $service->id,
                    'language_id'  => $language->id,
                ]));
            }
        });
    }
}
