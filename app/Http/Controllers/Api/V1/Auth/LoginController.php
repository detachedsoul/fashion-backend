<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = Auth::user();

        if ($user->status !== AccountStatus::Active) {
            return response()->error(
                message: 'This account is not active. Contact support.',
                errors: null,
                status: 403
            );
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->error(
                message: 'Please verify your email address before logging in. Check your inbox for the verification link.',
                errors: null,
                status: 403,
            );
        }

        $token = $user->createToken(
            $request->input('device_name') ?: ($request->userAgent() ?: 'unknown-device'),
            ['*'],
        )->plainTextToken;

        $user->forceFill(['last_login_at' => now()])->save();

        return response()->success(
            data: new UserResource($user, $token),
            message: 'Login successful.',
        );
    }
}
