<?php

namespace App\Http\Controllers;

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

class ServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index(): JsonResponse
    {
        $query = Service::with([
                'provider:id,name',
                'photos',
                'favoritedBy:id',
                'currency',
                'translations.language',
            ])
            ->filter()
            ->latest()
            ->paginate(10); //end of database query

            $services = $query->through(function($service){
                $service->is_favorited = $service->favoritedBy->pluck('id')->contains( request('user_id') );
                return $service;
            })
            ->withQueryString();

        return $this->success([
                    // 'services' => $services->items(),
                    'services' => ServiceResource::collection($services->items()),
                    'last_page' => $services->lastPage(),
                    // 'lang' => Language::getCurrentLanguageId(),
                    // 'query' => getSqlWithBindings($query)
                ],'Services fetched successfully'
        );
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

    public function update(UpdateServiceRequest $request, Service $service, Language $language)
    {
        $service->update($request->except('photos', 'translations', 'provider_id'));

        $languages = $language->pluck('id', 'code');
        foreach ($request->translations as $translation) {
            $service->translations()
                ->updateOrCreate(
                    ['id' => $translation['id']], // or use ['language_id' => $translation['language']['id']]
                    [
                        'name' => $translation['name'] ?? '',
                        'description' => $translation['description'] ?? '',
                        'language_id' => $languages[$translation['language']['code']] ?? null,
                    ]
                );
        }

        return $this->success($service, 'Service updated successfully');
    }

}
