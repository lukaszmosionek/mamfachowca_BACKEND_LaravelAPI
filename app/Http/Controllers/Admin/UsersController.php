<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Role;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

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

    public function index(){

        $users = $this->users->allWithTrashed();

        return $this->success([
            'users' => UserResource::collection($users),
        ], 'Users fetched successfully');
    }

    public function show($userId){

        $perPage = request('per_page');

        $user = $this->users->findWithAvailabilities($userId);
        $services = $this->services->getUserServicesWithPhotos($userId, $perPage);

        return $this->success([
            'user' => new UserResource($user),
            'services' => ServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'User fetched successfully');
    }

    public function destroy($userId)
    {
        try {
            $user = $this->users->findWithTrashed($userId);
            $message = $this->users->toggleDelete($user);

            return $this->success($message, 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
