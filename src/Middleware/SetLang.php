<?php

namespace Flamix\Lang\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class SetLang
{
    public function handle($request, Closure $next)
    {
        app()->setLocale(lang()->get());
        return $next($request);
    }
}
