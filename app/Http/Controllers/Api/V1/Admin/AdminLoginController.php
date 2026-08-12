<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Resources\AdminResource;
use Illuminate\Http\JsonResponse;

class AdminLoginController extends Controller
{
    public function __invoke(AdminLoginRequest $request): JsonResponse
    {
        $admin = $request->authenticate();

        if ($admin->status !== AccountStatus::Active) {
            return response()->error(
                message: 'This account is not active.',
                errors: null,
                status: 403
            );
        }

        if (! $admin->hasVerifiedEmail()) {
            return response()->error(
                'Please verify your email address before logging in. Check your inbox for the verification link.',
                null,
                403,
            );
        }

        $token = $admin->createToken(
            $request->input('device_name') ?: ($request->userAgent() ?: 'unknown-device'),
            ['*'],
        )->plainTextToken;

        $admin->forceFill(['last_login_at' => now()])->save();

        return response()->success(
            data: new AdminResource($admin, $token),
            message: 'Login successful.',
        );
    }
}
