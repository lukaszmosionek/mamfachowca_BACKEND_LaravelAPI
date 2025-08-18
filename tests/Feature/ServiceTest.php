<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Currency;
use App\Models\Photo;
use App\Models\Translation;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_services_with_pagination()
    {
        // Create a user
        $user = User::factory()->create();

        // Create related models
        $provider = User::factory()->create(['role'=>'provider']);
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
        $response = $this->getJson(route('services.index', ['user_id' => $user->id]));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'services' => [
                             '*' => [
                                 'id',
                                 'name',
                                 'is_favorited',
                                 'provider',
                                 'photos',
                                 'currency',
                                 'translations',
                             ]
                         ],
                         'last_page'
                     ]
                 ]);

        // Assert pagination returns correct number
        $this->assertCount(10, $response->json('data.services'));

        // Assert that the first service is marked as favorited
        $firstService = $response->json('data.services')[0];
        $this->assertTrue($firstService['is_favorited']);
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
                            'availabilities' => [
                                '*' => [
                                    'id',
                                    'day',
                                    'start_time',
                                    'end_time',
                                ]
                            ]
                        ],
                        'photos' => [
                            '*' => ['id', 'url']
                        ],
                        'translations' => [
                            '*' => [
                                'id',
                                'language' => ['id', 'code'],
                                'title',
                                'description',
                            ]
                        ]
                    ]
                ]
            ]);

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
