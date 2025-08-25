<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Str;
use App\Http\Resources\UserServiceResource;
use App\Models\Language;
use App\Models\Photo;
use App\Models\Service;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index()
    {
        $services = auth()->user()->services()->with('photos','translations.language')->latest()->paginate(10);
        return $this->success([
            'services' => UserServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'Services fetched successfully');
    }

    public function store(StoreServiceRequest $request, ImageService $imageService, Language $language)
    {
        $service = auth()->user()->services()->create($request->all());

        if( $request->photos ){
            foreach ( $request->file('photos') as $photo) {
                $paths[] = [
                    'original' => Photo::storeFile($photo['file']),
                    'original_filename' => Str::limit( $photo['file']->getClientOriginalName(), 255, '')
                ];
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

    public function show($id): JsonResponse
    {
        $service = Service::with([
            'provider:id,name',
            'provider.availabilities',
            'photos',
            'translations.language:id,code'
        ])->findOrFail($id);

        $service = new ServiceResource($service);
        return $this->success(compact('service'), 'Service fetched successfully');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return $this->success(null, 'Service deleted successfully', 204);
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

    public function destroyPhoto($id)
    {
        $photo = Photo::findOrFail($id);

        Storage::disk('public')->delete( Photo::getSizeKeys() );
        $photo->delete();

        return $this->success(null , 'Photo deleted');
    }

}
