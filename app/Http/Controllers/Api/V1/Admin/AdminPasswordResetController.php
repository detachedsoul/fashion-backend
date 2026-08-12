<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminForgotPasswordRequest;
use App\Http\Requests\Admin\AdminResetPasswordRequest;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AdminPasswordResetController extends Controller
{
    public function sendResetLink(AdminForgotPasswordRequest $request): JsonResponse
    {
        Password::broker('admins')->sendResetLink($request->only('email'));

        return response()->success(
            message: 'If an admin account exists for that email, a reset link has been sent.',
        );
    }

    public function reset(AdminResetPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $admin, string $password): void {
                $admin->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $admin->tokens()->delete();

                event(new PasswordReset($admin));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->error(
                message: 'Unable to reset your password. The reset link may be invalid or expired.',
                errors: null,
                status: 422
            );
        }

        return response()->success(
            message: 'Your password has been reset successfully.',
        );
    }
}
