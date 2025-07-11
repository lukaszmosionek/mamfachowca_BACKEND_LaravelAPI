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

    public function all(){
        $search = request('name');
        $provider_id = request('provider_id');

        $services = Service::with(['provider:id,name','provider.availabilities'])
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

    public function index()
    {
        // Pokaż usługi zalogowanego usługodawcy
        $services = auth()->user()->services()->get();
        $services = ServiceResource::collection($services);
        return $this->success($services, 'Services fetched successfully');
    }

    public function store(StoreServiceRequest $request)
    {
        $service = auth()->user()->services()->create($request->all());
        return $this->success($service, 'Service created successfully', 201);
    }

    public function show(Service $service)
    {
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
}
