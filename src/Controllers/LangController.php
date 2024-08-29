<?php

namespace Flamix\Lang\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class LangController extends Controller
{
    public function get(): string
    {
        $drivers = config('lang.drivers.get');
        foreach ($drivers as $driver) {
            $lang = app($driver)->detect();
            if ($lang) {
                break;
            }
        }

        return $lang ?? config('app.locale', 'en');
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

    public function change(string $lang): RedirectResponse
    {
        $drivers = config('lang.drivers.get');
        $cookie = \Flamix\Lang\Drivers\Cookies::class;
        // Check if language is available
        throw_unless(app($cookie)->isAvailable($lang), \Exception::class, "Language {$lang} is not available!");
        // Set to all drivers
        $this->set($lang);
        // Special case for cookies
        if (in_array($cookie, $drivers)) {
            return back()->withCookie(app($cookie)->set($lang));
        }
        // Redirect back
        return back();
    }
}
