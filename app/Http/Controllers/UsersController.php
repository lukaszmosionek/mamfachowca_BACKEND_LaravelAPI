<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use ApiResponse;

    public function show($userId){

        $perPage = request('per_page');

        $user = User::with('availabilities')->findOrFail($userId);
        $services = $user->services()->with('photos')->paginate($perPage);

        return $this->success([
            'user' => new UserResource($user),
            'services' => ServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'User fetched successfully');
    }
}
