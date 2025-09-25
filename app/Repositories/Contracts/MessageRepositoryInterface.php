<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface MessageRepositoryInterface
{
    /**
     * Get messages exchanged between two users.
     *
     * @param int $authUserId
     * @param int $receiverId
     * @return Collection
     */
    public function getConversation(int $authUserId, int $receiverId): Collection;
}
