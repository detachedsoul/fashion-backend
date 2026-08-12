<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminChangePasswordRequest;
use App\Http\Requests\Admin\AdminUpdateProfileRequest;
use App\Http\Resources\AdminResource;
use App\Notifications\Admin\AdminConfirmEmailChangeNotification;
use App\Notifications\Admin\AdminEmailChangedNotification;
use App\Notifications\Admin\AdminPasswordChangedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class AdminProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->success(
            data: new AdminResource($request->user(), $request->bearerToken()),
            message: 'Admin profile retrieved successfully.'
        );
    }

    public function update(AdminUpdateProfileRequest $request): JsonResponse
    {
        $admin = $request->user();
        $admin->fill($request->safe()->except(['email', 'current_password']));

        $emailChangeRequested = $request->filled('email')
            && $request->string('email')->value() !== $admin->email;

        $pendingEmail = null;

        if ($emailChangeRequested) {
            $pendingEmail = $request->string('email')->value();
            $admin->pending_email = $pendingEmail;
        }

        $admin->save();

        if ($emailChangeRequested) {
            Notification::route('mail', $admin->email)
                ->notify(new AdminEmailChangedNotification($admin->email, $pendingEmail));

            Notification::route('mail', $pendingEmail)
                ->notify(new AdminConfirmEmailChangeNotification($admin->id, $pendingEmail));
        }

        return response()->success(
            data: new AdminResource($admin),
            message: $emailChangeRequested
                ? 'Profile updated. Check your new email address to confirm the change.'
                : 'Profile updated successfully.',
        );
    }

    public function changePassword(AdminChangePasswordRequest $request): JsonResponse
    {
        $admin = $request->user();
        $admin->forceFill(['password' => Hash::make($request->string('password'))])->save();

        $admin->tokens()->where('id', '!=', $admin->currentAccessToken()->id)->delete();

        $admin->notify(new AdminPasswordChangedNotification);

        return response()->success(message: 'Password updated successfully.');
    }

    public function cancelPendingEmail(Request $request): JsonResponse
    {
        $admin = $request->user();

        if (! $admin->pending_email) {
            return response()->error(
                message: 'There is no pending email change to cancel.',
                errors: null,
                status: 404
            );
        }

        $admin->forceFill(['pending_email' => null])->save();

        return response()->success(message: 'Pending email change cancelled.');
    }
}
