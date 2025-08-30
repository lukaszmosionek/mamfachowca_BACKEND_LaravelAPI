<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition(): array
    {
        return [
            'user1_id' => User::factory(),
            'user2_id' => User::factory(),
            'last_message_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];

    }
}
