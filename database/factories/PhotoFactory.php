<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo_path' => 'uploads/photos/' . fake()->image('public/storage/uploads/photos', 640, 480, null, false),
            'imageable_id' => null,
            'imageable_type' => null,
        ];
    }
}
