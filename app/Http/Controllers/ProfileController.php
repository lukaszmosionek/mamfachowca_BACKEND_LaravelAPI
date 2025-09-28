<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use App\Services\AvatarService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function getUser(Request $request): JsonResponse
    {
        return $this->success([
            'user' => new UserResource( auth()->user() )
        ], 'User fetched successfully');
    }

    public function update(UpdateUserRequest $request, UserService $userService): JsonResponse
    {
        $user = $userService->updateUser($request->user(), $request->validated());

        return $this->success([
            'user' => new UserResource($user)
        ], 'User updated successfully', 201);
    }

    public function uploadAvatar(UploadAvatarRequest $request, AvatarService $avatarService): JsonResponse
    {
        $avatarUrl = $avatarService->updateUserAvatar($request->user(), $request->file('avatar'));

        return $this->success( compact('avatarUrl'), 'Avatar Uploaded');
    }
}
