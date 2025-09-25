<?php

namespace App\Repositories;

use App\Models\Service;
use App\Models\User;

class FavoriteRepository
{
    public function findServiceById(int $id): Service
    {
        return Service::findOrFail($id);
    }

    public function isFavorited(User $user, int $serviceId): bool
    {
        return $user->favorites()->where('service_id', $serviceId)->exists();
    }

    public function attach(User $user, int $serviceId): void
    {
        $user->favorites()->attach($serviceId);
    }

    public function detach(User $user, int $serviceId): void
    {
        $user->favorites()->detach($serviceId);
    }
}
