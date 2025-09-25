<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function getProviders();
    public function findByIdWithAvailabilities(int $userId): User;
    public function getUserServices(int $userId, ?int $perPage = 15): LengthAwarePaginator;
    public function allWithTrashed(): Collection;
    public function findWithAvailabilities(int $id): User;
    public function getUserServicesWithPhotos(int $id, int $perPage = 15): LengthAwarePaginator;
    public function toggleDelete(User $user): string;
    public function findWithTrashed(int $id): User;

}
