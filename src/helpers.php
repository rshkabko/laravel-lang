<?php

if (!function_exists('lang')) {
    function lang()
    {
        return app(\Flamix\Lang\Controllers\LangController::class);
    }
}

if (!function_exists('lang_prefixes')) {
    /**
     * Locale prefixes for registering page routes: ['en', 'ru', 'ua'].
     * With prefix_fallback off adds a bare '' prefix, so the lang-prefix
     * middleware redirects unprefixed URLs instead of the global fallback.
     */
    function lang_prefixes(): array
    {
        $prefixes = array_keys(config('lang.available', []));

        if (!config('lang.prefix_fallback', true)) {
            $prefixes[] = '';
        }

        return $prefixes;
    }
}

if (!function_exists('lang_route')) {
    function lang_route($name, $parameters = [], $absolute = true)
    {
        return route(app()->getLocale() . '.' . $name, $parameters, $absolute);
    }
}