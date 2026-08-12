<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Revoke the token used to authenticate the current request only
     * (i.e. log out this device, leave other sessions active).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->success(message: 'Logged out successfully.');
    }

    /**
     * Revoke every token for the user (log out of all devices).
     */
    public function all(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->success(message: 'Logged out of all devices.');
    }
}
