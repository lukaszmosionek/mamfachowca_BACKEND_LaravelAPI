<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getProviders()
    {
        // Fetch only providers
        return User::select(['id', 'name'])
            ->where('role', 'provider')
            ->pluck('name', 'id');
    }

    public function findByIdWithAvailabilities(int $userId): User
    {
        return User::with('availabilities')->findOrFail($userId);
    }

    public function getUserServices(int $userId, ?int $perPage = 15): LengthAwarePaginator
    {
        $user = $this->findByIdWithAvailabilities($userId);

        return $user->services()->with('photos')->paginate($perPage);
    }

}
