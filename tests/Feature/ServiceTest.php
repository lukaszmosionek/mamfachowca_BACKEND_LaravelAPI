<?php

namespace Tests\Feature;

use App\Enum\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_services_with_pagination()
    {
        $startTime = microtime(true);
        // Create a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create related models
        $provider = User::factory()->create(['role'=>Role::PROVIDER]);
        $currency = Currency::factory()->create();

        // Create some services
        $services = Service::factory()
            ->count(15)
            ->for($provider, 'provider')
            ->for($currency)
            ->create();

        // Add favorites for the user
        $services[0]->favoritedBy()->attach($user->id);

        // Hit the endpoint
        $response = $this->getJson("/api/services?user_id={$user->id}");

        $response->assertStatus(200)
                 ->assertJson([ 'success' => true ]);

        // Assert pagination returns correct number
        $this->assertCount(10, $response->json('data.services'));

        // Assert that the first service is marked as favorited
        $firstService = $response->json('data.services')[0];
        $this->assertTrue($firstService['is_favorited']);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        dump('executionTime ' . $executionTime . ' seconds');
    }

    public function test_search_works_properly()
    {
        $language = Language::factory()->create([ 'code' => 'en' ]);
        App::setLocale('en');

        $provider = User::factory()->create([ 'role' => Role::PROVIDER ]);

        $service = Service::factory()
            ->for($provider, 'provider')
            ->withTranslations()
            ->create();

        $user = User::factory()->create();
        $service->favoritedBy()->attach($user->id);

        // Act
        $response = $this->getJson("/api/services?name=rvic&provider_id={$provider->id}");

        // Assert
        $response->assertOk();
    }

    public function test_it_returns_a_service_with_its_relationships()
    {
        // Arrange: create service and related models
        $service = Service::factory()->create();

        // Act: hit the endpoint
        $response = $this->getJson('/api/services/'.$service->id);

        // Assert: response structure and status
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $service->id,
            'name' => $service->name,
        ]);
    }

    public function test_it_returns_404_if_service_not_found()
    {
        $response = $this->getJson('/api/services/9999999');
        $response->assertStatus(404);
    }
}
