<?php

namespace App\Traits;

use App\Enums\Enums\StatusCode;
use Illuminate\Http\JsonResponse;

trait HttpResponses
{
    public function success($data, $message = 'Okay', $code = StatusCode::Success->value): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $code);
    }

    public function failed($data, $code, $message = 'Failed'): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $code);
    }
}
