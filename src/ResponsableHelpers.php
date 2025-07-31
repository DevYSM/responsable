<?php


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
