<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $client = User::factory()->create(['role' => 'client']);
        $service = Service::factory()->for($provider, 'provider')->create();
        $date = now()->addDays(rand(1, 10))->toDateString();
        $start = now()->setTime(rand(8, 16), 0)->format('H:i');
        $duration = $service->duration_minutes ?? 30;

        return [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            // 'service_id' => $service->id,
            'date' => $date,
            'start_time' => $start,
            'end_time' => date('H:i', strtotime("$start + $duration minutes")),
            'status' => 'pending',
        ];
    }
}
