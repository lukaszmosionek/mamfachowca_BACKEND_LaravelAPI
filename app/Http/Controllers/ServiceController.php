<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
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
        $search = request('name');
        $provider_id = request('provider_id');

        if( $provider_id OR $search ) request()->merge(['page' => 1]); // Force page 1

        $services = Service::with(['provider:id,name', 'photos' ,'favoritedBy:id'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($provider_id, function ($query, $provider_id) {
                $query->where('provider_id', $provider_id);
            })
            ->latest()
            // ->where('lang', App::getLocale())
            ->paginate(10)
            ->through(function($service){
                $service->is_favorited = in_array( request('user_id'), $service->favoritedBy->pluck('id')->toArray() );
                return $service;
            })
            ->withQueryString();

        return $this->success([
                    'data' => ServiceResource::collection($services->items()),
                    'last_page' => $services->lastPage()
                ],'Services fetched successfully'
        );
    }

    public function show($id): JsonResponse
    {
        $service = Service::with(['provider:id,name','provider.availabilities', 'photos'])->findOrFail($id);
        return $this->success( new ServiceResource($service),'Service fetched successfully');
    }

}
