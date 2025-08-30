<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {

        // Ensure there are users and a chat to attach to
        $sender = User::factory();
        $receiver = User::factory();
        $chat = Chat::factory(); // assuming you have a ChatFactory

        return [
            'chat_id' => $chat->create()->id,
            'sender_id' => $sender->create()->id,
            'receiver_id' => $receiver->create()->id,
            'body' => $this->faker->sentence(),
        ];

    }
}
