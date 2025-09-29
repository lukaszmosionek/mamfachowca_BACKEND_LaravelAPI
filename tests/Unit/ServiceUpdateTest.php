<?php

namespace Tests\Feature;

use App\Enum\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceUpdateTest extends TestCase
{
    use RefreshDatabase;

    private $payload = [
            'name' => 'Updated Service Name',
            'price' => 100,
            'duration_minutes' => 60,
            'translations' => [
                [
                    'id' => null,
                    'name' => 'Translated Name',
                    'description' => 'Translated Description',
                    'language' => ['code' => 'en'],
                ]
            ]
        ];

    public function test_authorized_user_can_update_service()
    {
        $provider = User::factory()->create(['role' => Role::PROVIDER]);
        $service = Service::factory()->for($provider, 'provider')->create();
        $language = Language::factory()->create(['code' => 'en']);

        $this->actingAs($provider);

        $response = $this->putJson(route('me.services.update', [
            'service' => $service->id,
            'language' => $language->id,
        ]), $this->payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['success' => true]);

        // $this->assertDatabaseHas('services', ['name' => 'Updated Service Name']);
        $this->assertDatabaseHas('service_translations', [
            'name' => 'Translated Name',
            'description' => 'Translated Description',
        ]);
    }

    public function test_other_provider_cannot_update_service()
    {
        $provider1 = User::factory()->create(['role' => Role::PROVIDER]);
        $provider2 = User::factory()->create(['role' => Role::PROVIDER]);
        $service = Service::factory()->for($provider1, 'provider')->withTranslations()->create();
        $language = Language::factory()->create(['code' => 'en']);

        $this->actingAs($provider2);

        $response = $this->putJson(route('me.services.update', [
            'service' => $service->id,
            'language' => $language->id,
        ]), [
            'name' => 'Test',
            'price' => 100,
            'duration_minutes' => 30
        ]);

        dump($response->getContent());

        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_can_update_every_service()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $service = Service::factory()->withTranslations()->create();
        $language = Language::factory()->create(['code' => 'en']);

        $this->actingAs($admin);

        $response = $this->putJson(route('me.services.update', [
            'service' => $service->id,
            'language' => $language->id,
        ]), $this->payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['success' => true]);

        // $this->assertDatabaseHas('services', ['name' => 'Updated Service Name']);
        $this->assertDatabaseHas('service_translations', [
            'name' => 'Translated Name',
            'description' => 'Translated Description',
        ]);
    }

    public function test_unauthorized_user_cannot_update_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->withTranslations()->create();
        $language = Language::factory()->create();

        $this->actingAs($user);

        $response = $this->putJson(route('me.services.update', [
            'service' => $service->id,
            'language' => $language->id,
        ]), [
            'name' => 'Test',
            'price' => 100,
            'duration_minutes' => 30
        ]);

        $response->assertStatus(403); // Forbidden
    }
}
