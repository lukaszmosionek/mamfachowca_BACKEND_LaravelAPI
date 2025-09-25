<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public function updateUserAvatar(User $user, UploadedFile $file): string
    {
        $path = $file->store('avatars', 'public');

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => $path]);

        return Storage::disk('public')->url($path);
    }
}
