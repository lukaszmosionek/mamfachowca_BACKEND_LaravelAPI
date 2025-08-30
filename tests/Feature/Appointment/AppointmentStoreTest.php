<?php

namespace Tests\Feature\Appointment;

use App\Enum\AppointmentStatus as EnumAppointmentStatus;
use App\Enum\Role;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAppointmentNotification;
use Tests\TestCase;

class AppointmentStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake(); // Prevent actual notifications
    }

    public function test_client_can_create_appointment_successfully()
    {
        // $client = User::factory()->client()->create();
        // $provider = User::factory()->provider()->create();
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        $client = User::factory()->create(['role'=>Role::CLIENT]);
        $service = Service::factory()->create([
            'provider_id' => $provider->id,
            'duration_minutes' => 60,
        ]);

        $this->actingAs($client);

        $response = $this->postJson('/api/appointments', [
            'service_id' => $service->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true ]);

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'status' => EnumAppointmentStatus::Pending,
        ]);

        Notification::assertSentTo($provider, NewAppointmentNotification::class);
    }

    public function test_cannot_create_appointment_with_invalid_data()
    {
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        $client = User::factory()->create(['role'=>Role::CLIENT]);
        $this->actingAs($client);

        $response = $this->postJson('/api/appointments', [
            'service_id' => 999, // non-existing service
            'date' => 'invalid-date',
            'start_time' => '25:00',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['service_id', 'date', 'start_time']);
    }

    public function test_non_client_cannot_create_appointment()
    {
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        $client = User::factory()->create(['role'=>Role::CLIENT]);
        $this->actingAs($provider);

        $response = $this->postJson('/api/appointments', [
            'service_id' => 1,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00',
        ]);

        $response->assertStatus(403); // Forbidden
    }
}
