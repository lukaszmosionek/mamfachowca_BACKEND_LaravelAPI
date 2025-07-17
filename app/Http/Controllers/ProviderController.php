<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProviderController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index()
    {
        // Pokaż usługi zalogowanego usługodawcy
        // $services = auth()->user()->services()->get();
        $providers = User::select($fields)->where('role', 'provider')->get();
        // $providers = ServiceResource::collection($providers);
        return $this->success($providers, 'Providers fetched successfully');
    }


}
