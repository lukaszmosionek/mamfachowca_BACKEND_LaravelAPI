<?php

namespace App\Http\Controllers;

use App\Enum\Role;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServicePhotoRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserServiceResource;
use App\Models\Service;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use App\Repositories\Contracts\UserServiceRepositoryInterface;
use App\Repositories\UserServiceRepository;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class UserServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    protected UserServiceRepositoryInterface $serviceRepository;
    protected PhotoRepositoryInterface $photoRepository;
    protected ImageService $imageService;

    public function __construct(UserServiceRepositoryInterface $serviceRepository, PhotoRepositoryInterface $photoRepository, ImageService $imageService)
    {
        $this->serviceRepository = $serviceRepository;
        $this->photoRepository = $photoRepository;
        $this->imageService = $imageService;
    }

    public function index(UserServiceRepository $repository): JsonResponse
    {
        $services = $repository->getUserServices(auth()->user());

        return $this->success([
            'services' => UserServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'Services fetched successfully');
    }

    public function store(StoreServiceRequest $request, UserServiceRepositoryInterface $repository): JsonResponse
    {
        $service = $repository->createService($request->all());

        if ($request->has('photos')) {
            $repository->addPhotos($service, $request->file('photos'));
        }

        if ($request->has('translations')) {
            $repository->addTranslations($service, $request->translations);
        }

        return $this->success([
            'service' => new UserServiceResource($service)
        ], 'Service created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $service = $this->serviceRepository->findByIdWithRelations($id);

        return $this->success([
            'service' => new ServiceResource($service),
        ], 'Service fetched successfully');
    }

    public function update(UpdateServiceRequest $request, Service $service, UserServiceRepositoryInterface $repository): JsonResponse
    {
        if( auth()->user()->role !== Role::ADMIN && auth()->user()->id !== $service->provider_id ) return $this->error('You can only update your own service.', 403);

        // Update main service fields
        $service = $repository->updateService(
            $service,
            $request->except('photos', 'translations', 'provider_id')
        );

        // Update translations
        if ($request->has('translations')) {
            $repository->updateTranslations($service, $request->translations);
        }

        return $this->success([
            'service' => new UserServiceResource($service)
        ], 'Service updated successfully');
    }

    public function destroy(Service $service): JsonResponse
    {
        if( auth()->id() !== $service->provider_id ) return $this->error('You can only delete your own services.', 403);

        $service->delete();
        return $this->success(null, 'Service deleted successfully', 204);
    }

    public function storePhotos(Service $service, UpdateServicePhotoRequest $request): JsonResponse
    {
        $photoModel = [];

        if ($request->hasFile('photos')) {
            $photoModel = $this->photoRepository->storeForService(
                $service,
                $request->file('photos')
            );
        }

        return $this->success([
            'photos' => PhotoResource::collection(collect($photoModel))
        ], 'Photos uploaded successfully!');
    }

    public function destroyPhoto(int $id): JsonResponse
    {
        $photo = $this->photoRepository->findById($id);

        $this->imageService->deletePhotoFiles($photo);
        $this->photoRepository->delete($photo);

        return $this->success(null, 'Photo deleted successfully!');
    }

}
