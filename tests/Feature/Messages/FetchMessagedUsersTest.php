<?php

namespace Tests\Feature\Messages;

use Tests\TestCase;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchMessagedUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_users_that_the_authenticated_user_has_chatted_with()
    {
        // Create users
        $authUser = User::factory()->create();
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Create chats
        Chat::factory()->create([
            'user1_id' => $authUser->id,
            'user2_id' => $userA->id,
            'last_message_at' => now()->subMinutes(5),
        ]);

        Chat::factory()->create([
            'user1_id' => $userB->id,
            'user2_id' => $authUser->id,
            'last_message_at' => now(),
        ]);

        // Authenticate as $authUser
        $this->actingAs($authUser);

        // Call the route
        $response = $this->getJson( route('fetchMessagedUsers') );

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Users fetched successfully',
                 ])
                 ->assertJsonFragment(['id' => $userB->id]) // Should appear first (latest chat)
                 ->assertJsonFragment(['id' => $userA->id]); // Should appear second
    }

    public function test_it_returns_empty_list_if_no_chats_exist()
    {
        $authUser = User::factory()->create();

        $this->actingAs($authUser);

        $response = $this->getJson( route('fetchMessagedUsers') );

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Users fetched successfully',
                     'data' => ['usersYouChattedWith' => []],
                 ]);
    }

    public function test_it_requires_authentication()
    {
        $response = $this->getJson( route('fetchMessagedUsers') );

        $response->assertStatus(401);
    }
}
