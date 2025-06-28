<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_appointment()
    {
        $client = User::factory()->create(['role' => 'client']);
        $provider = User::factory()->create(['role' => 'provider']);
        $service = Service::factory()->for($provider)->create(['duration_minutes' => 30]);

        $response = $this->actingAs($client, 'sanctum')->postJson('/api/appointments', [
            'service_id' => $service->id,
            'provider_id' => $provider->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('appointments', ['client_id' => $client->id]);
    }

    public function test_provider_cannot_create_appointment()
    {
        $provider = User::factory()->create(['role' => 'provider']);

        $response = $this->actingAs($provider, 'sanctum')->postJson('/api/appointments', [
            'service_id' => 1,
            'provider_id' => 1,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
        ]);

        $response->assertForbidden();
    }

    public function test_client_can_view_own_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->for($client, 'client')->create();

        $response = $this->actingAs($client, 'sanctum')->getJson("/api/appointments/{$appointment->id}");

        $response->assertOk();
    }

    public function test_client_cannot_view_others_appointments()
    {
        $userA = User::factory()->create(['role' => 'client']);
        $userB = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->for($userA, 'client')->create();

        $response = $this->actingAs($userB, 'sanctum')->getJson("/api/appointments/{$appointment->id}");

        $response->assertForbidden();
    }
}
