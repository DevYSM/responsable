<?php

namespace YSM\Responsable\Macros;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

/**
 * @method static JsonResponse error(string $message = '', int $code = 422, array $errors = [])
 */
class ErrorMacro
{
    /**
     * Register services.
     */
    public static function register(): void
    {
        Response::macro('error', function (string $message = '', int $code = 422, array $errors = []): JsonResponse {
            return response()->json([
                'status' => false,
                'message' => $message,
                'code' => $code,
                'errors' => $errors,
            ], $code);
        });
    }

}
