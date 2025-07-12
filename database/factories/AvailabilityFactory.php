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
        $start = $this->faker->dateTimeBetween('08:00:00', '12:00:00')->format('H:i:s');
        $end = \Carbon\Carbon::parse($start)->addHours(rand(2,7))->format('H:i:s');

        $day_of_week = [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ];

        return [
            'provider_id' => User::factory(),
            'day_of_week' => $this->faker->randomElement($day_of_week),
            'start_time' => $start,
            'end_time' => $end,
        ];
    }
}
