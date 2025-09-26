<?php

namespace App\Repositories;

use App\Enum\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function getProviders()
    {
        // Fetch only providers
        return User::select(['id', 'name'])
            ->where('role', Role::PROVIDER)
            ->get();
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

    public function allWithTrashed(): Collection
    {
        return User::withTrashed()->get();
    }

    public function findWithAvailabilities(int $id): User
    {
        return User::with('availabilities')->findOrFail($id);
    }

    public function getUserServicesWithPhotos(int $id, int $perPage = 15): LengthAwarePaginator
    {
        $user = User::findOrFail($id);

        return $user->services()->with('photos')->paginate($perPage);
    }

    public function findWithTrashed(int $id): User
    {
        return User::withTrashed()->findOrFail($id);
    }

    public function toggleDelete(User $user): string
    {
        if ($user->role === Role::ADMIN) {
            throw new \Exception('Admins cannot be deleted', 403);
        }

        if ($user->trashed()) {
            $user->restore();
            return 'User restored successfully.';
        }

        $user->delete();
        return 'User soft-deleted successfully';
    }

}
