<?php

namespace YSM\Responsable\Macros\Api;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;

/**
 * @method static JsonResponse success(string $message = '', array|object $data = [], int $code = 200, Paginator|LengthAwarePaginator|CursorPaginator $paginator = null)
 */
class ApiSuccessMacro
{
    /**
     * Register services.
     */
    public static function register(): void
    {
        Response::macro('success', function (
            string                                         $message = '',
            array|object                                   $data = [],
            int                                            $code = 200,
            Paginator|LengthAwarePaginator|CursorPaginator $paginator = null
        ): JsonResponse {

            $response = [
                'status' => true,
                'message' => $message,
                'code' => $code,
                'data' => $data,
            ];

            if ($paginator) {
                $response['meta'] = match (true) {
                    $paginator instanceof LengthAwarePaginator => [
                        'total' => $paginator->total(),
                        'per_page' => $paginator->perPage(),
                        'current_page' => $paginator->currentPage(),
                        'last_page' => $paginator->lastPage(),
                        'first_item' => $paginator->firstItem(),
                        'lastItem' => $paginator->lastItem(),
                    ],

                    $paginator instanceof Paginator => [
                        'per_page' => $paginator->perPage(),
                        'current_page' => $paginator->currentPage(),
                        'has_more_pages' => $paginator->hasMorePages(),
                    ],

                    $paginator instanceof CursorPaginator => [
                        'per_page' => $paginator->perPage(),
                        'has_more_pages' => $paginator->hasMorePages(),
                        'next_cursor' => $paginator->nextCursor()?->encode(),
                        'prev_cursor' => $paginator->previousCursor()?->encode(),
                    ],
                    default => null,
                };
            }

            return response()->json($response, $code);
        });

    }

}
