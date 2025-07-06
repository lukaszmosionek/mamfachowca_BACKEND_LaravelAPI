<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function all(){
        $services = Service::with('provider:id,name')->paginate(request('per_page', 10))->withQueryString();

        return $this->success(
            ServiceResource::collection($services)->response()->getData(true),
            'Services fetched successfully'
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
        $service = auth()->user()->services()->create($request);
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
