<?php

use App\Http\Controllers\Api\V1\Admin\AdminEmailVerificationController;
use App\Http\Controllers\Api\V1\Admin\AdminLoginController;
use App\Http\Controllers\Api\V1\Admin\AdminLogoutController;
use App\Http\Controllers\Api\V1\Admin\AdminPasswordResetController;
use App\Http\Controllers\Api\V1\Admin\AdminProfileController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Admin endpoints
    Route::prefix('auth/admin')->name('admin.')->group(function () {

        Route::middleware('throttle:admin-auth')->group(function () {
            Route::post('login', AdminLoginController::class)
                ->name('login');

            Route::post('forgot-password', [AdminPasswordResetController::class, 'sendResetLink'])
                ->name('password.email');

            Route::post('reset-password', [AdminPasswordResetController::class, 'reset'])
                ->name('password.reset');

            Route::post('email/resend', [AdminEmailVerificationController::class, 'resend'])
                ->name('email.resend');
        });

        Route::get('email/verify/{id}/{hash}', [AdminEmailVerificationController::class, 'verify'])
            ->middleware('signed')
            ->name('email.verify');

        Route::get('email/confirm-change/{id}/{hash}', [AdminEmailVerificationController::class, 'confirmChange'])
            ->middleware('signed')
            ->name('email.confirm-change');

        Route::middleware('auth:admin')->group(function () {
            Route::post('logout', AdminLogoutController::class)
                ->name('logout');

            Route::post('logout-all', [AdminLogoutController::class, 'all'])
                ->name('logout.all');

            Route::get('me', [AdminProfileController::class, 'show'])
                ->name('me');

            Route::patch('me', [AdminProfileController::class, 'update'])
                ->name('me.update');

            Route::put('me/password', [AdminProfileController::class, 'changePassword'])
                ->name('me.password');

            Route::delete('me/pending-email', [AdminProfileController::class, 'cancelPendingEmail'])
                ->name('me.pending-email.cancel');
        });
    });

    // User auth endpoints
    Route::prefix('auth')->name('auth.')->group(function () {

        Route::middleware('throttle:auth')->group(function () {
            Route::post('register', RegisterController::class)
                ->name('register');

            Route::post('login', LoginController::class)
                ->name('login');

            Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])
                ->name('password.email');

            Route::post('reset-password', [PasswordResetController::class, 'reset'])
                ->name('password.reset');

            Route::post('email/resend', [EmailVerificationController::class, 'resend'])
                ->name('email.resend');
        });

        Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            ->middleware('signed')
            ->name('email.verify');

        Route::get('email/confirm-change/{id}/{hash}', [EmailVerificationController::class, 'confirmChange'])
            ->middleware('signed')
            ->name('email.confirm-change');

        // Authenticated-only routes.
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', LogoutController::class)
                ->name('logout');

            Route::post('logout-all', [LogoutController::class, 'all'])
                ->name('logout.all');

            Route::get('me', [ProfileController::class, 'show'])
                ->name('me');

            Route::patch('me', [ProfileController::class, 'update'])
                ->name('me.update');

            Route::put('me/password', [ProfileController::class, 'changePassword'])
                ->name('me.password');

            Route::delete('me/pending-email', [ProfileController::class, 'cancelPendingEmail'])
                ->name('me.pending-email.cancel');
        });
    });

    // Everything below this line requires a verified, authenticated user.
    // Catalog/orders/referrals/loyalty route groups get added here as those
    // modules are built.
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        //
    });
});
