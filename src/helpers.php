<?php

if (!function_exists('lang')) {
    function lang()
    {
        return app(\Flamix\Lang\Controllers\LangController::class);
    }
}
