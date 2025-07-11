<?php

namespace Database\Factories;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    public function definition()
    {
        $start = $this->faker->time('H:i:s');
        $end = \Carbon\Carbon::parse($start)->addHours(1)->format('H:i:s');

        $day_of_week = [
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday'
        ];

        return [
            'provider_id' => User::factory(),
            'day_of_week' => $this->faker->randomElement($day_of_week),
            'start_time' => $start,
            'end_time' => $end,
        ];
    }
}
