<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $referrer = $request->referrer();

        $user = DB::transaction(function () use ($request, $referrer): User {
            return User::create([
                'name' => $request->string('name'),
                'email' => $request->string('email'),
                'phone' => $request->string('phone')->value(),
                'password' => Hash::make($request->string('password')),
                'referred_by_user_id' => $referrer?->id,
            ]);
        });

        event(new Registered($user));

        return response()->success(
            data: new UserResource($user),
            message: 'Registration successful. Check your email to verify your account before logging in.',
            status: 201,
        );
    }
}
