<?php

namespace App\Http\Controllers;

use App\Actions\CreateAvailabilityAction;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $user = User::create( $request->all() + ['lang' => App::getLocale()] );
        $user->role = $request->role;  //validated in Request that is not ADMIN
        $user->save();

        if($request->availability){
            app(CreateAvailabilityAction::class)->execute($user, $request->availability);
        }

        return $this->success([
                'user' => $user,
                'token' => $user->createToken('api_token')->plainTextToken,
            ], 'User registered successfully.', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401 , ['password' => 'Invalid credentials']);
        }

        return $this->success([
                'user' => $user,
                'token' => $user->createToken('api_token')->plainTextToken,
            ], 'User logged in successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->success(null, 'Logged out');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return config('paths.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        });


        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? $this->success(null, __($status))
            : $this->error(__($status), 400, ['email' => __($status)]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->success(null, __($status))
            : $this->error(__($status), 400);
    }
}
