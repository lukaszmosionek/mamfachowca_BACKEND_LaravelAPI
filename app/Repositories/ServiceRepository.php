<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function getPaginatedServices($perPage = 10)
    {
        return Service::with([
                'provider:id,name',
                'photos',
                'favoritedBy:id',
                'currency',
                'translations.language',
            ])
            ->filter()
            ->latest()
            ->paginate($perPage);
    }

    public function findByIdWithRelations($id)
    {
        return Service::with([
                'provider:id,name',
                'provider.availabilities',
                'photos',
                'translations.language:id,code',
            ])->findOrFail($id);
    }

    public function getFavoritedByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Service::with([
                'provider:id,name',
                'photos',
                'favoritedBy:id',
                'currency',
                'translations.language',
            ])
            ->filter()
            ->whereHas('favoritedBy', function($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->latest()
            ->paginate($perPage)
            ->through(function($service){
                $service->is_favorited = true;
                return $service;
            })
            ->withQueryString();
    }

}
