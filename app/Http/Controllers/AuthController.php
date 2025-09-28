<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Password;
use App\Services\PasswordResetService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    use ApiResponse;

    protected UserService $userService;
    protected PasswordResetService $passwordResetService;

    public function __construct(UserService $userService, PasswordResetService $passwordResetService)
    {
        $this->userService = $userService;
        $this->passwordResetService = $passwordResetService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->register($request->all());

        return $this->success([
                'user' => new UserResource($user),
                'token' => $user->createToken('api_token')->plainTextToken,
            ], 'User registered successfully.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->userService->login($request->email, $request->password);

        if (!$user) {
            return $this->error('Invalid credentials', 401, ['password' => 'validation.invalid-credentials']);
        }

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $user->createToken('api_token')->plainTextToken,
        ], 'User logged in successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->userService->logout($request->user());
        return $this->success(null, 'Logged out');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->passwordResetService->sendResetLink($request->email);

        return $status === Password::RESET_LINK_SENT
            ? $this->success(null, __($status))
            : $this->error(__($status), 400, ['email' => __($status)]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->passwordResetService->resetPassword(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );

        return $status === Password::PASSWORD_RESET
            ? $this->success(null, __($status))
            : $this->error(__($status), 400);
    }
}
