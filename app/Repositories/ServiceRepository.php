<?php

namespace App\Repositories;

use App\Models\Service;

class ServiceRepository
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
}
