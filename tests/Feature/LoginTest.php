<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_user_can_login_with_correct_credentials()
    {
        // Arrange: create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: send login request
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert: check response
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user',
                         'token',
                     ],
                 ]);

        $this->assertTrue($response['success']);
        $this->assertEquals('User logged in successfully.', $response['message']);
    }

    #[Test]
    public function test_user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid credentials',
                     'errors' => [
                         'password' => 'Invalid credentials',
                     ],
                 ]);
    }

    #[Test]
    public function test_user_cannot_login_with_nonexistent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid credentials',
                     'errors' => [
                         'password' => 'Invalid credentials',
                     ],
                 ]);
    }

    #[Test]
    public function test_email_and_password_are_required()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422) // validation error
                 ->assertJsonValidationErrors(['email', 'password']);
    }
}
