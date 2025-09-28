<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\ServiceResource;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Services\ServiceService;

class ServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    protected ServiceRepositoryInterface $serviceRepository;
    protected ServiceService $serviceService;

    public function __construct(ServiceRepositoryInterface $serviceRepository, ServiceService $serviceService)
    {
        $this->serviceRepository = $serviceRepository;
        $this->serviceService = $serviceService;
    }

    public function index(): JsonResponse
    {
        $services = $this->serviceRepository->getPaginatedServices();

        return $this->success([
                    'services' => ServiceResource::collection($services->items()),
                    'last_page' => $services->lastPage()
                ],'Services fetched successfully'
        );
    }

    public function show(int $id): JsonResponse
    {
        $service = $this->serviceRepository->findByIdWithRelations($id);

        return $this->success([
                    'service' => new ServiceResource($service),
                ], 'Service fetched successfully'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $message = $this->serviceService->toggleDelete($id);
        return $this->success($message, 200);
    }

}
