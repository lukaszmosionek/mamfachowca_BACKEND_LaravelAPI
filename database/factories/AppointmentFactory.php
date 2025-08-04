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
        $provider = fn(array $attributes) => $attributes['provider_id'] ?? User::factory()->create(['role' => 'provider'])->id;
        $client = fn(array $attributes) => $attributes['client_id'] ?? User::factory()->create(['role' => 'client'])->id;
        $service = fn(array $attributes) => $attributes['service_id'] ?? Service::factory()->for($provider, 'provider')->create()->id;
        $date = now()->addDays(rand(1, 10))->toDateString();
        $start = now()->setTime(rand(8, 16), 0)->format('H:i');
        $duration = $service->duration_minutes ?? 30;

        return [
            'client_id' => $client,
            'provider_id' => $provider,
            'service_id' => $service,
            'date' => $date,
            'start_time' => $start,
            'end_time' => date('H:i', strtotime("$start + $duration minutes")),
            'status' => 'pending',
        ];
    }
}
