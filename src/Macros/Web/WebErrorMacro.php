<?php

namespace YSM\Responsable\Macros\Web;

use Illuminate\Http\RedirectResponse;

/**
 * @method static error(string $message = '', int $code = 422, array $errors = [], bool $persist = false)
 */
class WebErrorMacro
{
    /**
     * Register services.
     */
    public static function register(): void
    {
        RedirectResponse::macro('error', function (
            string $message = '',
            int    $code = 422,
            array  $errors = [],
            bool   $persist = false
        ) {
            $method = $persist ? 'put' : 'flash';

            session()->$method('response_type', 'error');
            session()->$method('message', $message);
            session()->$method('code', $code);
            session()->$method('errors', $errors);

            return $this;
        });

    }

}
