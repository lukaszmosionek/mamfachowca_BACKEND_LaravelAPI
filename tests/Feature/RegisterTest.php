<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_user_can_register_successfully()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'client',
            // 'availability' => ['monday' => true], // adjust to your action’s needs
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertCreated()
                 ->assertJson([
                     'success' => true,
                     'message' => 'User registered successfully.',
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user' => [
                             'id',
                             'name',
                             'email',
                             'lang',
                             'created_at',
                             'updated_at',
                         ],
                         'token',
                     ],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'lang' => App::getLocale(),
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    #[Test]
    public function test_registration_requires_valid_data()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name',
                     'email',
                     'password',
                     'role',
                 ]);
    }

    #[Test]
    public function test_email_must_be_unique()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $payload = [
            'name' => 'Jane',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'user',
            'availability' => ['monday' => true],
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}
