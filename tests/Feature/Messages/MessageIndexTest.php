<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_all_messages_between_authenticated_user_and_target_user()
    {
        // Create two users
        $authUser = User::factory()->create();
        $targetUser = User::factory()->create();

        // Create messages between them
        $message1 = Message::factory()->create([
            'sender_id' => $authUser->id,
            'receiver_id' => $targetUser->id,
            'body' => 'Hello from authUser'
        ]);

        $message2 = Message::factory()->create([
            'sender_id' => $targetUser->id,
            'receiver_id' => $authUser->id,
            'body' => 'Reply from targetUser'
        ]);

        // Acting as the authenticated user
        $response = $this->actingAs($authUser)
                         ->getJson(route('users.messages.index', ['user' => $targetUser->id]));

        $response->assertStatus(200)
                 ->assertJsonFragment(['body' => 'Hello from authUser'])
                 ->assertJsonFragment(['body' => 'Reply from targetUser']);
    }

    public function test_it_returns_empty_if_no_messages_exist()
    {
        $authUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($authUser)
                         ->getJson(route('users.messages.index', ['user' => $targetUser->id]));

        $response->assertStatus(200)
                 ->assertJsonFragment(['messages' => []]);
    }
}
