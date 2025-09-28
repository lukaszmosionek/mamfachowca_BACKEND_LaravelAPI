<?php

namespace App\Http\Controllers;


use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    use ApiResponse;

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show(int $id): JsonResponse
    {

        $perPage = request('per_page');

        $user = $this->userRepository->findByIdWithAvailabilities($id);
        $services = $this->userRepository->getUserServices($id, $perPage);

        return $this->success([
            'user' => new UserResource($user),
            'services' => ServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'User fetched successfully');
    }
}
