<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    use ApiResponse;

    protected UserRepositoryInterface $users;
    protected ServiceRepositoryInterface $services;

    public function __construct(UserRepositoryInterface $users, ServiceRepositoryInterface $services)
    {
        $this->users = $users;
        $this->services = $services;
    }

    public function index(): JsonResponse
    {

        $users = $this->users->allWithTrashed();

        return $this->success([
            'users' => UserResource::collection($users),
        ], 'Users fetched successfully');
    }

    public function show($id): JsonResponse
    {

        $perPage = request('per_page');

        $user = $this->users->findWithAvailabilities($id);
        $services = $this->services->getUserServicesWithPhotos($id, $perPage);

        return $this->success([
            'user' => new UserResource($user),
            'services' => ServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'User fetched successfully');
    }

    public function destroy($id): JsonResponse
    {
        try {
            $user = $this->users->findWithTrashed($id);
            $message = $this->users->toggleDelete($user);

            return $this->success($message, 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
