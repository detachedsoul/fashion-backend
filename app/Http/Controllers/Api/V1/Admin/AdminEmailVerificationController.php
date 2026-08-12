<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminResendVerificationRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Notifications\Admin\AdminEmailChangeConfirmedNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;

class AdminEmailVerificationController extends Controller
{
    /**
     * Same "no bearer token available" reasoning as the customer
     * EmailVerificationController::verify() - opened directly from an
     * email client, so this checks the signed {id}/{hash} pair manually
     * rather than relying on an authenticated request.
     */
    public function verify(string $id, string $hash): JsonResponse
    {
        $admin = Admin::find($id);

        if (! $admin || ! hash_equals($hash, sha1($admin->getEmailForVerification()))) {
            abort(403, 'Invalid or expired verification link.');
        }

        if ($admin->hasVerifiedEmail()) {
            return response()->error(
                message: 'Email already verified.',
                errors: null,
                status: 409
            );
        }

        $admin->markEmailAsVerified();
        event(new Verified($admin));

        $token = $admin->createToken('email-verification', ['*'])->plainTextToken;

        return response()->success(
            data: new AdminResource($admin, $token),
            message: 'Email verified successfully.',
        );
    }

    /**
     * Public - same reasoning as the customer resend(): login is blocked
     * pre-verification, so this can't require an authenticated admin.
     */
    public function resend(AdminResendVerificationRequest $request): JsonResponse
    {
        $email = mb_strtolower($request->string('email')->value());
        $key = 'admin-verify-email:'.$email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->error(
                message: 'Too many requests. Please wait before requesting another link.',
                errors: null,
                status: 429
            );
        }

        RateLimiter::hit($key, 300); // 3 per 5 minutes

        $admin = Admin::where('email', $email)->first();

        if ($admin && ! $admin->hasVerifiedEmail()) {
            $admin->sendEmailVerificationNotification();
        }

        return response()->success(
            message: 'If the email is registered and unverified, a new verification link has been sent.',
        );
    }

    /**
     * Confirms a PENDING email change - see pending_email column +
     * AdminProfileController::update(). Hash is checked against
     * pending_email specifically, never the live email column.
     */
    public function confirmChange(string $id, string $hash): JsonResponse
    {
        $admin = Admin::find($id);

        if (! $admin || ! $admin->pending_email || ! hash_equals($hash, sha1($admin->pending_email))) {
            abort(403, 'Invalid or expired email change link.');
        }

        $newEmail = $admin->pending_email;

        if (Admin::where('email', $newEmail)->where('id', '!=', $admin->id)->exists()) {
            $admin->forceFill(['pending_email' => null])->save();
            abort(409, 'This email address is already in use.');
        }

        $oldEmail = $admin->email;

        $admin->forceFill([
            'email' => $newEmail,
            'email_verified_at' => now(),
            'pending_email' => null,
        ])->save();

        $admin->notify(new AdminEmailChangeConfirmedNotification($oldEmail, $newEmail));

        return response()->success(message: 'Email address changed successfully.');
    }
}
