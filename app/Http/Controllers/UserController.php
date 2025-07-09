<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function getUser(Request $request){
        return $this->success($request->user(), 'User fetched successfully');
    }

    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();
        $user->update($request->all());

        return $this->success($user, 'User updated successfully', 201);
    }
}
