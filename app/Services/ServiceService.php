<?php

namespace App\Services;

use App\Repositories\Contracts\ServiceRepositoryInterface;

class ServiceService
{
    protected ServiceRepositoryInterface $repository;

    public function __construct(ServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function toggleDelete(int $id): string
    {
        $service = $this->repository->findWithTrashed($id);

        if ($service->trashed()) {
            $this->repository->restore($service);
            return 'Service restored successfully.';
        }

        $this->repository->softDelete($service);
        return 'Service soft-deleted successfully.';
    }
}
