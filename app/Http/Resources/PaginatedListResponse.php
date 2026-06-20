<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedListResponse
{
    public static function make(ResourceCollection $collection, int $total, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $collection->resolve(request()),
            'total' => $total,
        ], $status);
    }
}
