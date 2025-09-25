<?php

namespace App\Repositories;

use App\Models\Chat;
use App\Models\User;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Support\Collection;

class ChatRepository implements ChatRepositoryInterface
{
    public function getMessagedUsers(int $userId): Collection
    {
        $chatUserIds = Chat::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->latest('last_message_at')
            ->get()
            ->map(fn($chat) => $chat->user1_id === $userId ? $chat->user2_id : $chat->user1_id)
            ->unique()
            ->values();

        return User::whereIn('id', $chatUserIds)
            ->get(['id', 'name', 'email'])
            ->sortBy(fn($user) => array_search($user->id, $chatUserIds->toArray()))
            ->values();
    }
}
