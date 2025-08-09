<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\ServiceResource;
use App\Models\Photo;
use App\Models\Service;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index()
    {
        // Pokaż usługi zalogowanego usługodawcy
        $services = auth()->user()->services()->with('photos')->latest()->paginate(10);
        return $this->success([
            'services' => ServiceResource::collection($services),
            'last_page' => $services->lastPage(),

        ], 'Services fetched successfully');
    }

    public function store(StoreServiceRequest $request, ImageService $imageService)
    {
        $service = auth()->user()->services()->create($request->all());

        if( $request->photos ){
            foreach ( $request->photos as $photo) {
                $paths[] = $imageService->storeImageFromUrl( $photo['file'] );
            }
            $service->photos()->createMany( $paths );
        }

        return $this->success($service, 'Service created successfully', 201);
    }

    public function show($id)
    {
        $service = Service::with('photos')->findOrFail($id);
        $this->authorize('view', $service);
        $service = ServiceResource::collection($service);
        return $this->success($service, 'Service fetched successfully');
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);
        $service->update($request->validated());
        return $this->success($service, 'Service updated successfully');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return $this->success(null, 'Service deleted successfully', 204);
    }

    public function destroyPhoto($id)
    {
        $photo = Photo::findOrFail($id);

        Storage::disk('public')->delete( Photo::getSizeKeys() );
        $photo->delete();

        return $this->success(null , 'Photo deleted');
    }

    public function storePhotos(Service $service, Request $request, ImageService $imageService)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        foreach ($request->file('photos') as $photo) {
            $paths[] = $imageService->storeImageFromUrl($photo);
        }

        $photoModel = $service->photos()->createMany($paths);

        return $this->success([
            'photos' => PhotoResource::collection( $photoModel )
        ], 'Photos uploaded successfully!');
    }


}
