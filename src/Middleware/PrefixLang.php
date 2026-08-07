<?php

namespace Flamix\Lang\Middleware;

use Closure;
use Flamix\Lang\Drivers\Prefix;

class PrefixLang
{
    public function handle($request, Closure $next)
    {
        // Try to get prefix from URL
        $prefix_lang = app(Prefix::class)->detect();
        if ($prefix_lang) {
            app()->setLocale($prefix_lang);
            return $next($request);
        }

        // Hard Redirect to detected language, query string preserved
        return redirect()->to(lang()->prefixedUrl($request));
    }
}