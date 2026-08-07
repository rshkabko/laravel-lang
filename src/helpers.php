<?php

if (!function_exists('lang')) {
    function lang()
    {
        return app(\Flamix\Lang\Controllers\LangController::class);
    }
}

if (!function_exists('lang_route')) {
    function lang_route($name, $parameters = [], $absolute = true)
    {
        return route(app()->getLocale() . '.' . $name, $parameters, $absolute);
    }
}