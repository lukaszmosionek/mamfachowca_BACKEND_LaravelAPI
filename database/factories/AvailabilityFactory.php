<?php

namespace Database\Factories;

use App\Models\Availability;
use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    public function definition()
    {
        $startMinutes = rand(0, 24) * 10; // 0 to 240 in steps of 10
        $startTime = (new DateTime('08:00:00'))->add(new DateInterval("PT{$startMinutes}M"));
        $start = $startTime->format('H:i:s');
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
            'provider_id' => $attributes['provider_id'] ?? User::factory(),
            'day_of_week' => $this->faker->randomElement($day_of_week),
            'start_time' => $start,
            'end_time' => $end,
        ];
    }
}
