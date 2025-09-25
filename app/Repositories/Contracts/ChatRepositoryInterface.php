<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ChatRepositoryInterface
{
    /**
     * Get all users that the authenticated user has chatted with.
     *
     * @param int $userId
     * @return Collection
     */
    public function getMessagedUsers(int $userId): Collection;
}
