<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\EmailChangeConfirmedNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationController extends Controller
{
    /**
     * IMPORTANT: this route is opened directly from the user's email client,
     * so there is no Sanctum bearer token on the request - we cannot use
     * Laravel's stock EmailVerificationRequest (it expects an authenticated
     * session/token user). Instead we trust Laravel's 'signed' route
     * middleware (already validates the signature + 60-minute expiry before
     * this method runs) and manually confirm the {id}/{hash} pair matches a
     * real user, exactly like the framework does internally, just without
     * requiring prior auth. Route must be registered with ->middleware('signed').
     *
     * Returns JSON, not a redirect - whoever clicks this link in an email
     * client will see the raw response unless the frontend owns a page that
     * fetches this endpoint and renders its own UI from the result.
     */
    public function verify(string $id, string $hash): JsonResponse
    {
        $user = User::find($id);

        if (! $user || ! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid or expired verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return response()->error(
                message: 'Email already verified.',
                errors: null,
                status: 409
            );
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        $token = $user->createToken('email-verification', ['*'])->plainTextToken;

        return response()->success(
            data: new UserResource($user, $token),
            message: 'Email verified successfully.',
        );
    }

    /**
     * Public - takes an email address rather than relying on an
     * authenticated user, because login (and therefore holding a token) is
     * blocked until verification completes. Without this, a user who lost
     * or never received the original verification email would have no way
     * to request a new one.
     */
    public function resend(ResendVerificationRequest $request): JsonResponse
    {
        $email = mb_strtolower($request->string('email')->value());
        $key = 'verify-email:'.$email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->error(
                message: 'Too many requests. Please wait before requesting another link.',
                errors: null,
                status: 429
            );
        }

        RateLimiter::hit($key, 300);

        $user = User::where('email', $email)->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->success(message: 'If the email is registered and unverified, a new verification link has been sent.');
    }

    /**
     * Confirms a PENDING email change (see pending_email column + the
     * ProfileController::update() flow). Same "no bearer token available"
     * reasoning as verify() above - the hash is checked against
     * pending_email specifically, never against the live email column.
     */
    public function confirmChange(string $id, string $hash): JsonResponse
    {
        $user = User::find($id);

        if (! $user || ! $user->pending_email || ! hash_equals($hash, sha1($user->pending_email))) {
            abort(403, 'Invalid or expired email change link.');
        }

        $newEmail = $user->pending_email;

        // Someone else may have claimed this exact address in the meantime
        // (e.g. two independent pending requests for the same email) - fail
        // safely instead of hitting the `email` column's unique constraint.
        if (User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            $user->forceFill(['pending_email' => null])->save();

            abort(409, 'This email address is already in use.');
        }

        $oldEmail = $user->email;

        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => now(),
            'pending_email' => null,
        ])->save();

        $user->notify(new EmailChangeConfirmedNotification($oldEmail, $newEmail));

        return response()->success(message: 'Email address changed successfully.');
    }
}
