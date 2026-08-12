<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Notifications\ConfirmEmailChangeNotification;
use App\Notifications\EmailChangedNotification;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->success(
            message: 'User profile retrieved successfully.',
            data: new UserResource($request->user(), $request->bearerToken()),
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->except(['email', 'current_password']));

        $emailChangeRequested = $request->filled('email')
            && $request->string('email')->value() !== $user->email;

        $pendingEmail = null;

        if ($emailChangeRequested) {
            $pendingEmail = $request->string('email')->value();
            $user->pending_email = $pendingEmail;
        }

        $user->save();

        if ($pendingEmail !== null) {
            Notification::route('mail', $user->email)
                ->notify(new EmailChangedNotification($user->email, $pendingEmail));

            Notification::route('mail', $pendingEmail)
                ->notify(new ConfirmEmailChangeNotification($user->id, $pendingEmail));
        }

        return response()->success(
            data: new UserResource($user, $request->bearerToken()),
            message: $emailChangeRequested
                ? 'Profile updated. Check your new email address to confirm the change.'
                : 'Profile updated successfully.',
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->forceFill(['password' => Hash::make($request->string('password'))])->save();

        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        $user->notify(new PasswordChangedNotification);

        return response()->success(message: 'Password updated successfully.');
    }

    public function cancelPendingEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->pending_email) {
            return response()->error(
                message: 'There is no pending email change to cancel.',
                errors: null,
                status: 404
            );
        }

        $user->forceFill(['pending_email' => null])->save();

        return response()->success(message: 'Pending email change cancelled.');
    }
}
