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

        $user = User::with(['availabilities', 'services.photos'])->findorFail($userId);

        return $this->success($user, 'User fetched successfully');
    }
}
