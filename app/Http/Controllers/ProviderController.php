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
        $providers = User::select(['id','name'])->where('role', 'provider')->pluck('name', 'id');
        return $this->success( compact('providers'), 'Providers fetched successfully');
    }


}
