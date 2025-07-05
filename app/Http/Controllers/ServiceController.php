<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use AuthorizesRequests;

    public function all(){
        $services = Service::with('provider:id,name')->get();
        return response()->json($services);
    }

    public function index()
    {
        // Pokaż usługi zalogowanego usługodawcy
        $services = auth()->user()->services()->get();
        return response()->json($services);
    }

    public function store(StoreServiceRequest $request)
    {
        $service = auth()->user()->services()->create($request->validated());
        return response()->json($service, 201);
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);
        return response()->json($service);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);
        $service->update($request->validated());
        return response()->json($service);
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return response()->json(null, 204);
    }
}
