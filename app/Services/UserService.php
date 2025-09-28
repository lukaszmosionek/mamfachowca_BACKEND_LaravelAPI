<?php

namespace App\Services;

use App\Actions\CreateAvailabilityAction;
use App\Models\User;
use App\Repositories\FavoriteRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected CreateAvailabilityAction $createAvailabilityAction;
    protected UserRepository $userRepository;

    public function __construct(CreateAvailabilityAction $createAvailabilityAction, UserRepository $userRepository)
    {
        $this->createAvailabilityAction = $createAvailabilityAction;
        $this->userRepository = $userRepository;
    }

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

    public function register(array $data): User
    {
        // Force language based on app locale
        $data['lang'] = App::getLocale();

        /** @var User $user */
        $user = User::create($data);

        // Assign role
        $user->role = $data['role'];
        $user->save();

        // Handle availability if provided
        if (!empty($data['availability'])) {
            $this->createAvailabilityAction->execute($user, $data['availability']);
        }

        return $user;
    }

    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return null; // authentication failed
        }

        return $user;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    protected function updateAvatar(User $user, $avatar)
    {
        // Store and update avatar
    }
}
