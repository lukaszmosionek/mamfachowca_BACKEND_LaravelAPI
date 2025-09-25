<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function getFavoritedByUser(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function findByIdWithRelations(int $id);
}
