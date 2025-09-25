<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\FavoriteRepository;

class FavoriteService
{
    protected FavoriteRepository $repository;

    public function __construct(FavoriteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function toggle(User $user, int $serviceId): array
    {
        $this->repository->findServiceById($serviceId);

        if ($this->repository->isFavorited($user, $serviceId)) {
            $this->repository->detach($user, $serviceId);
            return ['favorited' => false];
        }

        $this->repository->attach($user, $serviceId);
        return ['favorited' => true];
    }
}
