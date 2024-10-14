<?php

namespace Flamix\Lang\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class PrefixLang
{
    public function handle($request, Closure $next)
    {
        // Try to get prefix from URL
        $prefix_lang = app(\Flamix\Lang\Drivers\Prefix::class)->detect();
        if ($prefix_lang) {
            app()->setLocale($prefix_lang);
            return $next($request);
        }

        // Hard Redirect to detected language
        $lang = lang()->get();
        return redirect()->to("/{$lang}/{$_SERVER['REQUEST_URI']}");
    }
}
