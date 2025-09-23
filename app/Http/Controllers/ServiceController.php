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

        $service = new ServiceResource($service);
        return $this->success(compact('service'), 'Service fetched successfully');
    }
}
