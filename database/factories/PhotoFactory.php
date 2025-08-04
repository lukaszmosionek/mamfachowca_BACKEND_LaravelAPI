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
            'thumbnail' => generatePlaceholder(300,300),
            'medium' => generatePlaceholder(300,300),
            'large' => generatePlaceholder(300,300),
            'imageable_id' => null,
            'imageable_type' => null,
        ];
    }
}
