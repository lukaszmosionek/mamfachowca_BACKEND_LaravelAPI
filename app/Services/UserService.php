<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\FavoriteRepository;

class UserService
{
    public function updateUser(User $user, array $data): User
    {
        // Example: handle avatar separately
        if (isset($data['avatar'])) {
            $this->updateAvatar($user, $data['avatar']);
            unset($data['avatar']);
        }

        $user->update($data);

        return $user;
    }

    protected function updateAvatar(User $user, $avatar)
    {
        // Store and update avatar
    }
}
