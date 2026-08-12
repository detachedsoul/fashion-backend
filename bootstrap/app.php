<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);

        $middleware->throttleApi();

        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (AuthenticationException $error, Request $request) {
            if ($request->is('api/*')) {
                return response()->error(
                    message: 'Unauthenticated.',
                    errors: null,
                    status: 401
                );
            }
        });

        $exceptions->render(function (AuthorizationException $error, Request $request) {
            if ($request->is('api/*')) {
                return response()->error(
                    message: $error->getMessage() ?: 'This action is unauthorized.',
                    errors: null,
                    status: 403
                );
            }
        });

        $exceptions->render(function (InvalidSignatureException $error, Request $request) {
            if ($request->is('api/*')) {
                return response()->error(
                    message: 'This link is invalid or has expired.',
                    errors: null,
                    status: 403
                );
            }
        });

        $exceptions->render(function (ValidationException $error, Request $request) {
            if ($request->is('api/*')) {
                $errors = $error->errors();
                $message = collect($errors)->flatten()->first();

                return response()->error(
                    message: $message,
                    errors: $errors,
                    status: $error->status ?? 422
                );
            }
        });

        $exceptions->render(function (HttpExceptionInterface $error, Request $request) {
            if ($request->is('api/*')) {
                $isLocal = App::isLocal();

                return response()->error(
                    message: $isLocal
                        ? ($error->getMessage() ?: 'Request failed.')
                        : 'Request failed.',
                    errors: $isLocal ? [
                        'message' => $error->getMessage(),
                        'trace' => $error->getTrace(),
                    ] : null,
                    status: $error->getStatusCode(),
                );
            }
        });

        $exceptions->render(function (Throwable $error, Request $request) {
            if ($request->is('api/*')) {
                return response()->error(
                    message: 'Something went wrong. Please try again.',
                    errors: App::isLocal() ? [
                        'message' => $error->getMessage(),
                        'trace' => $error->getTrace(),
                    ] : null,
                    status: 500
                );
            }
        });
    })->create();
