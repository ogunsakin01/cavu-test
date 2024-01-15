<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

trait ResponseHelper
{
    public function formattedResponse($response): JsonResponse
    {
        return response()->json([
            'message' => $response['message'],
            'data' => $response['data']
        ], $response['code'] ?? 200);
    }
}
