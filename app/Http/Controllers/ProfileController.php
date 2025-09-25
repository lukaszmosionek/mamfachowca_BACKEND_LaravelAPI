<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Photo;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse;

    public function getUser(Request $request){
        return $this->success( [
            'user' => new UserResource( auth()->user() )
        ], 'User fetched successfully');
    }

    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();
        $user->update($request->except('avatar'));

        return $this->success( compact('user'), 'User updated successfully', 201);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = auth()->user();
        $path = User::storeAvatarFile($request->file('avatar'));

        // Delete old avatar if exists
        if ($user->avatar) {
            User::deleteAvatarFile($user->avatar);
        }

        $user->avatar = $path;
        $user->save();

        return $this->success([
            'avatar_url' => User::getAvatarUrl($path)
        ], 'Avatar Uploaded');
    }
}
