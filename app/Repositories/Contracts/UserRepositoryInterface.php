<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getProviders();
    public function findByIdWithAvailabilities(int $userId): User;
    public function getUserServices(int $userId, ?int $perPage = 15): LengthAwarePaginator;
}
