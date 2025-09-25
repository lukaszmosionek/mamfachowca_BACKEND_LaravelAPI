<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Repositories\ServiceRepository;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    protected ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index(): JsonResponse
    {
        $services = $this->serviceRepository->getPaginatedServices();

        return $this->success([
            'services' => ServiceResource::collection($services->items()),
            'last_page' => $services->lastPage(),
        ], 'Services fetched successfully');
    }

    public function show(int $id): JsonResponse
    {
        $service = $this->serviceRepository->findByIdWithRelations($id);
        if (!$service) {
            return $this->error('Service not found', 404);
        }

        return $this->success([
            'service' => new ServiceResource($service)
        ], 'Service fetched successfully');
    }
}
