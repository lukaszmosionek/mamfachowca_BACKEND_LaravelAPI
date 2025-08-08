<?php

namespace Database\Factories;

use App\Models\Photo;
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
        $sizes = [];
        foreach(Photo::getSizes() as $name => $size){
            $sizes[$name] = generatePlaceholder($size['width'], $size['height']);
        }

        return [
            'imageable_id' => null,
            'imageable_type' => null,
        ]+$sizes;
    }
}
