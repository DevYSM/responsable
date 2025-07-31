<?php

namespace YSM\Responsable\Macros\Web;

use Illuminate\Http\RedirectResponse;

/**
 * @method static success(string $message = '', int $code = 200, array|object $data = [], bool $persist = false)
 */
class WebSuccessMacro
{
    /**
     * Register services.
     */
    public static function register(): void
    {
        RedirectResponse::macro('success', function (
            string       $message = '',
            int          $code = 200,
            array|object $data = [],
            bool         $persist = false
        ) {
            $method = $persist ? 'put' : 'flash';

            session()->$method('response_type', 'success');
            session()->$method('message', $message);
            session()->$method('code', $code);
            session()->$method('data', $data);

            return $this;
        });

    }

}
