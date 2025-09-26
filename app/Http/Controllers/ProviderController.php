<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProviderController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $providers = $this->userRepository->getProviders();
        return $this->success([
            'providers' => UserResource::collection($providers),
        ], 'Providers fetched successfully');
    }
}
