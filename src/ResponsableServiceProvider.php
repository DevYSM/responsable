<?php

namespace YSM\Responsable;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;
use YSM\Responsable\Macros\ErrorMacro;
use YSM\Responsable\Macros\SuccessMacro;

/**
 * @method static JsonResponse success(string $message = '', array|object $data = [], int $code = 200, Paginator|LengthAwarePaginator|CursorPaginator $paginator = null)
 * @method static JsonResponse error(string $message = '', int $code = 422, array $errors = [])
 */
class ResponsableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        SuccessMacro::register();
        ErrorMacro::register();
    }

    public function register(): void
    {
        //
    }
}
