<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         * General API rate limiter.
         */
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip());
        });

        /*
         * Stricter limiter for authentication endpoints.
         */
        RateLimiter::for('auth', function ($request) {
            return Limit::perMinute(10)
                ->by($request->ip());
        });

        RateLimiter::for('admin-auth', function ($request) {
            return Limit::perMinute(5)
                ->by($request->ip());
        });

        /*
         * Default password policy.
         */
        Password::defaults(function () {
            $rule = Password::min(5)
                ->letters()
                ->numbers();

            return App::isProduction()
                ? $rule->uncompromised()
                : $rule;
        });

        /*
         * Strict Eloquent behavior during development.
         */
        if (! App::isProduction()) {
            Model::shouldBeStrict();
        }

        /*
         * Force HTTPS in production.
         */
        if (App::isProduction()) {
            URL::forceScheme('https');
        }

        /**
         * Fail when unknown fields are passed
         */
        FormRequest::failOnUnknownFields();

        $this->registerResponseMacros();
    }

    /**
     * Central JSON response shape for the whole API: {success, message,
     * data, errors}. Registered on the Response facade (== the same
     * ResponseFactory instance the response() helper resolves), so
     * response()->success()/error() work identically in controllers AND in
     * bootstrap/app.php's exception render() closures - one shape, one
     * place it's defined, instead of hand-writing the array everywhere.
     */
    protected function registerResponseMacros(): void
    {
        Response::macro('success', function (mixed $data = null, string $message = 'Success', int $status = 200) {
            $payload = $data;

            if ($data instanceof ResourceCollection
                && $data->resource instanceof LengthAwarePaginator) {
                $paginator = $data->resource;

                $payload = [
                    'items' => $data,
                    'pagination' => [
                        'current_page' => $paginator->currentPage(),
                        'last_page' => $paginator->lastPage(),
                        'per_page' => $paginator->perPage(),
                        'total' => $paginator->total(),
                    ],
                ];
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $payload,
                'errors' => null,
            ], $status);
        });

        Response::macro('error', function (string $message = 'Something went wrong.', mixed $errors = null, int $status = 400) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'errors' => $errors,
            ], $status);
        });
    }
}
