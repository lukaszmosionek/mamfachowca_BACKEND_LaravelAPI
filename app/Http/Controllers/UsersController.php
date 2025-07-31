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

        $user = User::with('availabilities')->findOrFail($userId);
        $services = $user->services()->with('photos')->paginate(5);
        $user->services = $services;

        return $this->success($user, 'User fetched successfully');
    }
}
