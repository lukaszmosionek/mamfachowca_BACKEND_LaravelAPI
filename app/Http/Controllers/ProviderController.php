<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index()
    {
        // Pokaż usługi zalogowanego usługodawcy
        // $services = auth()->user()->services()->get();
        $providers = User::select(['id','name'])->where('role', 'provider')->get();
        // $providers = ServiceResource::collection($providers);
        return $this->success($providers, 'Providers fetched successfully');
    }


}
