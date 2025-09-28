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

class AppointmentShowTest extends TestCase
{
   use RefreshDatabase;

    public function test_client_can_view_their_own_appointment()
    {
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        $client = User::factory()->create(['role'=>Role::CLIENT]);
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
        ]);

        $this->actingAs($client)
             ->getJson(route('appointments.show', $appointment->id))
             ->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'appointment' => [
                         'id',
                         'service',
                         'provider',
                         'client',
                         // other fields depending on your AppointmentResource
                     ]
                 ]
             ]);
    }

    public function test_provider_can_view_their_own_appointment()
    {
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        $client = User::factory()->create(['role'=>Role::CLIENT]);
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
        ]);

        $this->actingAs($provider)
             ->getJson(route('appointments.show', $appointment->id))
             ->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'appointment' => [
                         'id',
                         'service',
                         'provider',
                         'client',
                     ]
                 ]
             ]);
    }

    public function test_user_cannot_view_appointments_they_do_not_belong_to()
    {
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        $client = User::factory()->create(['role'=>Role::CLIENT]);
        $otherUser = User::factory()->create();
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
        ]);

        $this->actingAs($otherUser)
             ->getJson(route('appointments.show', $appointment->id))
             ->assertStatus(403)
             ->assertJson([
                 'success' => false,
                 'message' => 'You can only view your own appointments.',
             ]);
    }
}
