<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_delete_service_they_do_not_own()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $service = Service::factory()->withTranslations()->create([
            'provider_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('me.services.destroy', $service->id));

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'You can only delete your own services.'
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
        ]);
    }

    public function test_user_can_delete_their_own_service()
    {
        $user = User::factory()->create();

        $service = Service::factory()->withTranslations()->create([
            'provider_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('me.services.destroy', $service->id));

        $response->assertStatus(204);

        $this->assertSoftDeleted('services', [
            'id' => $service->id,
        ]);
    }

}
