<?php

namespace Flamix\Lang\Controllers;

use Flamix\Lang\Drivers\Cookies;
use Flamix\Lang\Drivers\Prefix;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LangController extends Controller
{
    public function get(): string
    {
        foreach (config('lang.drivers.get') as $driver) {
            $lang = app($driver)->detect();
            if ($lang) {
                return $lang;
            }
        }

        return config('app.locale', 'en');
    }

    public function set(string $lang): string
    {
        $drivers = config('lang.drivers.set');
        // Set to all drivers
        foreach ($drivers as $driver) {
            app($driver)->set($lang);
        }

        return $lang;
    }

    /**
     * Current URL with the detected locale prefix, query string preserved.
     */
    public function prefixedUrl(?Request $request = null): string
    {
        $request ??= request();
        $path = trim($request->path(), '/');
        $query = $request->getQueryString();

        return '/' . $this->get() . ($path !== '' ? '/' . $path : '') . ($query ? '?' . $query : '');
    }

    /**
     * Fallback action: redirect a bare URL to its locale-prefixed version.
     */
    public function redirectToPrefix(Request $request): RedirectResponse
    {
        // Already prefixed and still unmatched — a real 404, don't loop
        abort_if(app(Prefix::class)->detect(), 404);

        return redirect()->to($this->prefixedUrl($request));
    }

    public function change(string $lang): RedirectResponse
    {
        // Check if language is available
        abort_unless(array_key_exists($lang, config('lang.available')), 404, "Language {$lang} is not available!");
        // Set to all drivers
        $this->set($lang);
        // Special case for cookies: queue it on the response
        if (in_array(Cookies::class, config('lang.drivers.set'))) {
            return back()->withCookie(app(Cookies::class)->set($lang));
        }
        // Redirect back
        return back();
    }
}