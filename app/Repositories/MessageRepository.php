<?php

namespace App\Repositories;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function getConversation(int $authUserId, int $receiverId): Collection
    {
        return Message::select(['sender_id', 'receiver_id', 'body'])
            ->where(function ($query) use ($authUserId, $receiverId) {
                $query->where('sender_id', $authUserId)
                      ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($authUserId, $receiverId) {
                $query->where('sender_id', $receiverId)
                      ->where('receiver_id', $authUserId);
            })
            ->orderBy('created_at')
            ->get();
    }
}
