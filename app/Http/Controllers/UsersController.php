<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
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
            'user' => $user,
            'services' => $services->items(),
            'last_page' => $services->lastPage(),
        ], 'User fetched successfully');
    }
}
