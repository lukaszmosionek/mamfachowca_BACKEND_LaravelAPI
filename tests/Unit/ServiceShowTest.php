<?php

namespace Tests\Feature;

use App\Enum\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_a_service_by_id_successfully()
    {
        // Arrange
        $provider = User::factory()->create(['role' => Role::PROVIDER]);
        $service = Service::factory()->withTranslations()
            ->for($provider, 'provider')
            ->hasPhotos(2)
            ->create();

        // Act
        $response = $this->getJson('/api/services/'.$service->id);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'service' => [
                        'id',
                        'name',
                        'provider' => [
                            'id',
                            'name',
                            'availabilities'
                        ],
                        'photos',
                        'translations'
                    ]
                ]
            ])
            ->assertJsonFragment([
                'message' => 'Service fetched successfully'
            ]);
    }

   public function test_it_returns_404_if_service_not_found()
    {
        $response = $this->getJson('/api/services/99999');

        $response->assertStatus(404);
    }
}
