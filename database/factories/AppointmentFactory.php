<?php

namespace Database\Factories;

use App\Enum\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        // Set date and start time
        $date = Carbon::now()->addDays(rand(1, 10))->toDateString();
        $start = Carbon::now()->setTime(rand(8, 16), 0);

        // Calculate end time based on service duration
        $end = $start->copy()->addMinutes($service->duration_minutes ?? 30);

        return [
            'client_id' => optional(User::where('role', 'client')->inRandomOrder()->first())->id ?? User::factory()->create(['role' => 'client']),
            'provider_id' => optional(User::where('role', 'provider')->inRandomOrder()->first())->id ?? User::factory()->create(['role' => 'provider']),
            'service_id' =>  optional(Service::inRandomOrder()->first())->id ?? Service::factory()->create(),
            'date' => $date,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'status' => AppointmentStatus::Pending,
        ];
    }
}
