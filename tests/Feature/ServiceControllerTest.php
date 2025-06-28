<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_create_service()
    {
        $provider = User::factory()->create(['role' => 'provider']);

        $response = $this->actingAs($provider, 'sanctum')->postJson('/api/services', [
            'name' => 'Masaż relaksacyjny',
            'description' => '60 minut masażu',
            'price' => 120,
            'duration_minutes' => 60,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('services', ['name' => 'Masaż relaksacyjny']);
    }

    public function test_client_cannot_create_service()
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client, 'sanctum')->postJson('/api/services', [
            'name' => 'Nielegalna usługa',
            'price' => 50,
            'duration_minutes' => 30,
        ]);

        $response->assertForbidden(); // ze względu na authorize() lub FormRequest::authorize
    }

    public function test_provider_can_update_own_service()
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $service = Service::factory()->for($provider)->create();

        $response = $this->actingAs($provider, 'sanctum')->putJson("/api/services/{$service->id}", [
            'name' => 'Nowa nazwa',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('services', ['id' => $service->id, 'name' => 'Nowa nazwa']);
    }

    public function test_provider_cannot_update_others_service()
    {
        $owner = User::factory()->create(['role' => 'provider']);
        $attacker = User::factory()->create(['role' => 'provider']);
        $service = Service::factory()->for($owner)->create();

        $response = $this->actingAs($attacker, 'sanctum')->putJson("/api/services/{$service->id}", [
            'name' => 'Próba włamania',
        ]);

        $response->assertForbidden();
    }
}
