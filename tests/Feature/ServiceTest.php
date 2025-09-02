<?php

namespace Tests\Feature;

use App\Enum\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Currency;
use App\Models\Photo;
use App\Models\Translation;
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
        $route = route('services.index', ['user_id' => $user->id]);
        $response = $this->getJson($route);

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
            ->withTranslation([
                'name' => 'Test Service',
                'language_id' => $language->id,
            ])
            ->withTranslation([
                'name' => 'Testzzzzzzz Service',
                'language_id' => 2,
            ])
            ->create();

        $user = User::factory()->create();
        $service->favoritedBy()->attach($user->id);

        // Act
        $response = $this->getJson(route('services.index', [
            'name' => 'rvic',
            'provider_id' => $provider->id,
            // 'user_id' => $user->id,
        ]));

        // $response->dump();
        // dump('service->toArray()', $service->translations);

        // Assert
        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'Test Service'
            ]);
        $response->assertJsonPath('data.services.0.provider.id', $provider->id);
    }

    // /** @test */
    // public function it_marks_services_as_not_favorited_for_other_users()
    // {
    //     $user = User::factory()->create();
    //     $otherUser = User::factory()->create();

    //     $service = Service::factory()->create();
    //     $service->favoritedBy()->attach($otherUser->id);

    //     $response = $this->getJson(route('services.index', ['user_id' => $user->id]));

    //     $response->assertStatus(200);

    //     $serviceData = collect($response->json('data.services'))->first();
    //     $this->assertFalse($serviceData['is_favorited']);
    // }

    public function test_it_returns_a_service_with_its_relationships()
    {
        // Arrange: create service and related models
        // $language = Language::factory()->create();
        // $provider = User::factory()->hasAvailabilities(3)->create();
        $service = Service::factory()->create();
        // dd($service);
        // ->for($provider)
        // ->has(Photo::factory()->count(2))
        // ->has(Translation::factory()->for($language))
        // ->create();

        // Act: hit the endpoint
        $response = $this->getJson(route('services.show', $service->id));


        // Assert: response structure and status
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $service->id,
            'name' => $service->name,
        ]);
    }

    public function test_it_returns_404_if_service_not_found()
    {
        $response = $this->getJson(route('services.show', 999999));

        $response->assertStatus(404);
    }
}
