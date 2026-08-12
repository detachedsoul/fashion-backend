<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLogoutController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->success(message: 'Logged out successfully.');
    }

    public function all(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->success(message: 'Logged out of all devices.');
    }
}
