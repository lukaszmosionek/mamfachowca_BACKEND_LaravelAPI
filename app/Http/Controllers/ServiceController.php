<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index(){
        $search = request('name');
        $provider_id = request('provider_id');

        $services = Service::with(['provider:id,name','provider.availabilities', 'photos'])
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when($provider_id, function ($query, $provider_id) {
                    $query->where('provider_id', $provider_id);
                })
                ->where('lang', App::getLocale())
                ->paginate(request('per_page', 10))
                ->withQueryString();

        return $this->success([
                    'data' => ServiceResource::collection($services->items()),
                    'total_pages' => $services->lastPage()
                ],'Services fetched successfully'
        );
    }

}
