<?php


use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;

/**
 * Retrieve session data stored by Responsable web macros.
 *
 * @return array<string, mixed>
 */
if (!function_exists('responsable')) {
    function responsable(): Fluent
    {
        return new Fluent([
            'type' => session('response_type'),
            'message' => session('message'),
            'code' => session('code'),
            'data' => session('data', []),
            'errors' => session('errors', []),
        ]);
    }
}

if (!function_exists('responsable_forget')) {
    /**
     * Forget session data stored by Responsable web macros.
     */
    function responsable_forget(): void
    {
        session()->forget(['response_type', 'message', 'code', 'data', 'errors']);
    }
}

if (!function_exists('apiSuccess')) {
    /**
     * Forget session data stored by Responsable web macros.
     */
    function apiSuccess(
        string                                         $message = '',
        array|object                                   $data = [],
        int                                            $code = 200,
        Paginator|LengthAwarePaginator|CursorPaginator $paginator = null
    ): \Illuminate\Http\JsonResponse
    {
        return Response::success(
            message: $message,
            data: $data,
            code: $code,
            paginator: $paginator
        );
    }
}

if (!function_exists('apiError')) {
    /**
     * Forget session data stored by Responsable web macros.
     */
    function apiError(string $message = '', int $code = 422, array $errors = []): \Illuminate\Http\JsonResponse
    {
        return Response::error(
            message: $message,
            code: $code,
            errors: $errors
        );
    }
}
