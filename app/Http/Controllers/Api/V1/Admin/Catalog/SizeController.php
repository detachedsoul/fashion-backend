<?php

namespace App\Http\Controllers\Api\V1\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreSizeRequest;
use App\Http\Requests\Admin\Catalog\UpdateSizeRequest;
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

    public function store(StoreSizeRequest $request): JsonResponse
    {
        $size = Size::create($request->validated());

        return response()->success(data: new SizeResource($size), message: 'Size created.', status: 201);
    }

    public function update(UpdateSizeRequest $request, Size $size): JsonResponse
    {
        $size->fill($request->validated())->save();

        return response()->success(data: new SizeResource($size), message: 'Size updated.');
    }

    public function destroy(Size $size): JsonResponse
    {
        // Safe to delete freely - product_variants nulls out size_id.
        $size->delete();

        return response()->success(message: 'Size deleted.');
    }
}
