<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;

class PasswordResetService
{
    public function sendResetLink(string $email): string
    {
        // Customize the reset password URL
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return config('paths.frontend_url')
                . '/reset-password?token=' . $token
                . '&email=' . urlencode($user->email);
        });

        // Send reset link
        return Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $credentials): string
    {
        return Password::reset(
            $credentials,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    }
}
