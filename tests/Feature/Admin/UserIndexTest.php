<?php

namespace Tests\Feature\Admin;

use App\Enum\Role;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_fetch_all_users_including_soft_deleted()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        // Create active users
        $users = User::factory()->count(3)->create();

        // Create soft-deleted users
        $deletedUsers = User::factory()->count(2)->create();
        $deletedUsers->each->delete();

        // Send request as admin
        $response = $this->actingAs($admin)->getJson('/api/admin/users');

        // Assert response is successful
        $response->assertStatus(200);

        // Assert that all users including soft deleted are present
        $response->assertJsonCount(3 + 3, 'data.users');
    }

    public function test_non_admin_cannot_fetch_users()
    {
        $user = User::factory()->create(['role' => Role::PROVIDER]);

        $response = $this->actingAs($user)->getJson('/api/admin/users');

        $response->assertStatus(403); // Or 401 based on your middleware
    }
}
