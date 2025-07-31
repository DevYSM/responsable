<?php

namespace YSM\Responsable;

use Illuminate\Support\ServiceProvider;
use YSM\Responsable\Macros\Api\ApiErrorMacro;
use YSM\Responsable\Macros\Api\ApiSuccessMacro;
use YSM\Responsable\Macros\Web\WebErrorMacro;
use YSM\Responsable\Macros\Web\WebSuccessMacro;


class ResponsableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ApiSuccessMacro::register();
        ApiErrorMacro::register();
        WebSuccessMacro::register();
        WebErrorMacro::register();
    }

    public function register(): void
    {
        require_once __DIR__ . '/ResponsableHelpers.php';
    }
}
