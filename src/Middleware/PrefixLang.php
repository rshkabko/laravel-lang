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

        // Permanent redirect to the detected language, query string preserved.
        // 301 so search engines canonicalise the prefixed URL (/ua) instead of
        // keeping the bare URL (/) as the homepage.
        return redirect()->to(lang()->prefixedUrl($request), 301);
    }
}