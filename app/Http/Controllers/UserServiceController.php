<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserServiceResource;
use App\Models\Language;
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
        $services = auth()->user()->services()->with('photos','translations.language')->latest()->paginate(10);
        return $this->success([
            'services' => UserServiceResource::collection( $services->items() ),
            // 'services' => $services->items(),
            'last_page' => $services->lastPage(),

        ], 'Services fetched successfully');
    }

    public function store(StoreServiceRequest $request, ImageService $imageService, Language $language)
    {
        $service = auth()->user()->services()->create($request->all());

        if( $request->photos ){
            foreach ( $request->photos as $photo) {
                $paths[] = $imageService->storeImageFromUrl( $photo['file'] );
            }
            $service->photos()->createMany( $paths );
        }

        $languages = $language::codeIdMap();
        foreach ( $request->translations as $translation) {
            $data[] = [
                'name' => $translation['name'] ?? '',
                'description' => $translation['description'] ?? '',
                'language_id' => $languages[$translation['language']['code']] ?? null,
            ];
        }
        $service->translations()->createMany($data);

        $service = new UserServiceResource($service);
        return $this->success(compact('service'), 'Service created successfully', 201);
    }

    public function show($id)
    {
        $service = Service::with('photos')->findOrFail($id);
        $this->authorize('view', $service);
        // $service = ServiceResource::collection($service);
        return $this->success($service, 'Service fetched successfully');
    }

    public function update(UpdateServiceRequest $request, Service $service, Language $language)
    {
        $this->authorize('update', $service);
        $service->update($request->except('translation'));

        $languages = $language->pluck('id', 'code');
        foreach ($request->translations as $translation) {
            $service->translations()
                ->updateOrCreate(
                    ['id' => $translation['id']], // or use ['language_id' => $translation['language']['id']]
                    [
                        'name' => $translation['name'],
                        'description' => $translation['description'],
                        'language_id' => $languages[$translation['language']['code']] ?? null,
                    ]
                );
        }

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
