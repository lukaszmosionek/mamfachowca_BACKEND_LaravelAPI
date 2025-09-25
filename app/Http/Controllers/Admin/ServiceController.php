<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Language;
use App\Models\Service;
use App\Services\CurrencyConversionService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        $service = new ServiceResource($service);
        return $this->success(compact('service'), 'Service fetched successfully');
    }

    public function destroy(int $serviceId): JsonResponse
    {
        $message = $this->serviceService->toggleDelete($serviceId);
        return $this->success($message, 200);
    }

}
