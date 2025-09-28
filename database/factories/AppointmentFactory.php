<?php

namespace Database\Factories;

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
        // Create or get provider
        $provider = User::factory()->create(['role' => 'provider']);

        // Create or get client
        $client = User::factory()->create(['role' => 'client']);

        // Create service for the provider
        $service = Service::factory()->for($provider, 'provider')->create();

        // Set date and start time
        $date = Carbon::now()->addDays(rand(1, 10))->toDateString();
        $start = Carbon::now()->setTime(rand(8, 16), 0);

        // Calculate end time based on service duration
        $end = $start->copy()->addMinutes($service->duration_minutes ?? 30);

        return [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'date' => $date,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'status' => 'pending',
        ];
    }
}
