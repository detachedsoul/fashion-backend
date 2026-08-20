<?php

use App\Http\Controllers\Api\V1\Admin\AdminEmailVerificationController;
use App\Http\Controllers\Api\V1\Admin\AdminLoginController;
use App\Http\Controllers\Api\V1\Admin\AdminLogoutController;
use App\Http\Controllers\Api\V1\Admin\AdminPasswordResetController;
use App\Http\Controllers\Api\V1\Admin\AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\Catalog\ClothingTypeController as AdminClothingTypeController;
use App\Http\Controllers\Api\V1\Admin\Catalog\ColorController as AdminColorController;
use App\Http\Controllers\Api\V1\Admin\Catalog\FabricController as AdminFabricController;
use App\Http\Controllers\Api\V1\Admin\Catalog\ProductionTierController as AdminProductionTierController;
use App\Http\Controllers\Api\V1\Admin\Catalog\SizeController as AdminSizeController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Catalog\ClothingTypeController;
use App\Http\Controllers\Api\V1\Catalog\ColorController;
use App\Http\Controllers\Api\V1\Catalog\DesignController;
use App\Http\Controllers\Api\V1\Catalog\FabricController;
use App\Http\Controllers\Api\V1\Catalog\ProductController;
use App\Http\Controllers\Api\V1\Catalog\ProductionTierController;
use App\Http\Controllers\Api\V1\Catalog\SizeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public catalog endpoints
    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::get('clothing-types', [ClothingTypeController::class, 'index'])
            ->name('clothing-types.index');
        Route::get('fabrics', [FabricController::class, 'index'])
            ->name('fabrics.index');
        Route::get('colors', [ColorController::class, 'index'])
            ->name('colors.index');
        Route::get('sizes', [SizeController::class, 'index'])
            ->name('sizes.index');
        Route::get('production-tiers', [ProductionTierController::class, 'index'])
            ->name('production-tiers.index');

        // Designs
        Route::get('designs', [DesignController::class, 'index'])
            ->name('designs.index');
        Route::get('designs/{design:slug}', [DesignController::class, 'show'])
            ->name('designs.show');

        // Products
        Route::get('products', [ProductController::class, 'index'])
            ->name('products.index');
        Route::get('products/{product:slug}', [ProductController::class, 'show'])
            ->name('products.show');
    });

    // Admin auth endpoints
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

    // Admin endpoints
    Route::prefix('admin/catalog')
        ->name('admin.catalog.')
        ->middleware(['auth:admin', 'permission:products.manage'])
        ->group(function () {
            Route::apiResource('clothing-types', AdminClothingTypeController::class)->except('show');
            Route::apiResource('fabrics', AdminFabricController::class)->except('show');
            Route::apiResource('colors', AdminColorController::class)->except('show');
            Route::apiResource('sizes', AdminSizeController::class)->except('show');
            Route::apiResource('production-tiers', AdminProductionTierController::class)->except('show');
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
