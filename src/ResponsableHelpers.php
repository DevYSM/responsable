<?php


use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Fluent;

/**
 * Retrieve session data stored by the Responsible web macros.
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
     * Forget session data stored by the Responsible web macros.
     */
    function responsable_forget(): void
    {
        session()->forget(['response_type', 'message', 'code', 'data', 'errors']);
    }
}

if (!function_exists('success')) {
    /**
     * Forget session data stored by the Responsible web macros.
     */
    function success(
        string                                         $message = '',
        array|object                                   $data = [],
        int                                            $code = 200,
        Paginator|LengthAwarePaginator|CursorPaginator|null $paginator = null
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

if (!function_exists('error')) {
    /**
     * Forget session data stored by the Responsible web macros.
     */
    function error(string $message = '', int $code = 422, array $errors = []): \Illuminate\Http\JsonResponse
    {
        return Response::error(
            message: $message,
            code: $code,
            errors: $errors
        );
    }
}
