<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\SizeResource;
use App\Models\Size;
use Illuminate\Http\JsonResponse;

class SizeController extends Controller
{
    public function index(): JsonResponse
    {
        $sizes = Size::query()->orderBy('sort_order')->get();

        return response()->success(data: SizeResource::collection($sizes));
    }
}
