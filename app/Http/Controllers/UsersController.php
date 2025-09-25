<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use ApiResponse;

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show($userId){

        $perPage = request('per_page');

        $user = $this->userRepository->findByIdWithAvailabilities($userId);
        $services = $this->userRepository->getUserServices($userId, $perPage);

        return $this->success([
            'user' => new UserResource($user),
            'services' => ServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'User fetched successfully');
    }
}
