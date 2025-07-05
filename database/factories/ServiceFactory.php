<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {

        $services = [
            'Fence Painting',
            'Washing Machine Repair',
            'House Cleaning',
            'Window Installation',
            'Roof Inspection',
            'Carpet Cleaning',
            'Lawn Mowing',
            'Gutter Cleaning',
            'Plumbing Repair',
            'Electrical Wiring',
            'Air Conditioner Servicing',
            'Refrigerator Repair',
            'Drywall Installation',
            'Deck Staining',
            'Garage Door Repair',
            'Tile and Grout Cleaning',
            'Pest Control',
            'Interior Painting',
            'Exterior Painting',
            'Water Heater Repair',
            'Chimney Sweeping',
            'Tree Trimming',
            'Security Camera Installation',
            'Smart Thermostat Setup',
            'Furniture Assembly',
            'Ceiling Fan Installation',
            'Pressure Washing',
            'Siding Repair',
            'Basement Waterproofing',
            'Driveway Sealing',
        ];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement($services),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(0, 50, 300),
            'duration_minutes' => $this->faker->numberBetween(15, 90),
        ];
    }
}
