<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse;

    public function getUser(Request $request){
        $user = auth()->user();
        $user = new UserResource($user);
        return $this->success( compact('user'), 'User fetched successfully');
    }

    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();
        $user->update($request->all());

        return $this->success( compact('user'), 'User updated successfully', 201);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = auth()->user();
        $path = $request->file('avatar')->store('avatars', 'public');

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = 'storage/'.$path;
        $user->save();

        return $this->success([
            'avatar_url' => asset("storage/{$path}")
        ], 'Avatar Uploaded');
    }
}
