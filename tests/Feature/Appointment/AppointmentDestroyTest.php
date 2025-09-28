<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_delete_their_own_appointment()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create([
            'client_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("api/appointments/".$appointment->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }

    public function test_a_user_cannot_delete_someone_elses_appointment()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $appointment = Appointment::factory()->create([
            'client_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete("api/appointments/".$appointment->id);

        $response->assertStatus(403)->assertJsonFragment([
             'message' => 'You can only delete your own appointments.'
         ]);

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
    }

    public function test_deleting_a_non_existent_appointment_returns_404()
    {
        $user = User::factory()->create();
        $nonExistentId = 9999; // some ID that doesn't exist in the database

        $response = $this->actingAs($user)
                        ->deleteJson("api/appointments/{$nonExistentId}");

        $response->assertStatus(404);
    }

}
