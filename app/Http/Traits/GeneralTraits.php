<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait GeneralTraits
{
    public function responseJSON($code = 200, $result = [], $message = ""): JsonResponse
    {
        $output = [
            'code' => $code,
            'results' => $result,
            'message' => $message,
        ];
        return response()->json($output, $code);
    }
}
