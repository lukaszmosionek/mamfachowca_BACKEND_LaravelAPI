<?php

namespace Tests\Feature\Http\Controllers;

use App\Enum\Role;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProviderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_providers_successfully()
    {
        // Arrange
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        User::factory()->create();
        $client = User::factory()->create(['role'=>Role::CLIENT]);

        // Act: Call the route (adjust route name if needed)
        $response = $this->getJson('/api/providers');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Providers fetched successfully',
                'data' => [
                    'providers' => [
                        $provider->id => $provider->name
                    ]
                ]
            ]);
    }
}
