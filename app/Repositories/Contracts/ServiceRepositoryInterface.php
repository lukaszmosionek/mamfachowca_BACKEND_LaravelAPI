<?php

namespace App\Repositories\Contracts;

use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function getFavoritedByUser(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function findByIdWithRelations(int $id);
    public function getUserServicesWithPhotos(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function findWithTrashed(int $id): Service;
    public function softDelete(Service $service): void;
    public function restore(Service $service): void;
}
