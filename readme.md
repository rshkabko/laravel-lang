# Laravel Lang

Language detection and switching for Laravel. Uses a driver-based approach: cookies, authenticated user, browser headers, URL prefix.

## Install

```bash
composer require flamix/laravel-lang
```

Publish config:

```bash
php artisan vendor:publish --provider="Flamix\Lang\ServiceProvider" --tag=config
```

## Configuration

```php
// config/lang.php
return [
    // Auto-register SetLang middleware in "web" group
    'autoload' => env('LANG_AUTOLOAD', true),

    // Available languages
    'available' => [
        'en' => 'English',
        'ru' => 'Русский',
        'ua' => 'Українська',
    ],

    // Detection drivers (checked top to bottom, first match wins)
    'drivers' => [
        'get' => [
            \Flamix\Lang\Drivers\Cookies::class,
            \Flamix\Lang\Drivers\AuthUser::class,  // requires `lang` column on users table
            \Flamix\Lang\Drivers\Browser::class,    // reads Accept-Language header
        ],
        // Where to persist when user switches language
        'set' => [
            \Flamix\Lang\Drivers\Cookies::class,
            \Flamix\Lang\Drivers\AuthUser::class,
        ],
    ],
];
```

## Usage

### Helpers

```php
// Get current language (runs through detection drivers)
lang()->get(); // "en"

// Set language (persists to all "set" drivers)
lang()->set('ru');

// Generate named route with locale prefix
lang_route('dashboard'); // route("en.dashboard")
lang_route('license', ['license_key' => $key]); // params are passed through

// Locale prefixes for registering page routes
lang_prefixes(); // ['en', 'ru', 'ua'] — plus '' when prefix_fallback is on
```

### Language switcher

Built-in route to change language via GET request:

```
GET /lang/change/{lang}
```

```html
<a href="{{ route('lang.change', 'en') }}">English</a>
<a href="{{ route('lang.change', 'ru') }}">Русский</a>
```

Sets the language in all configured `set` drivers and redirects back.

### Middleware

Two middlewares are registered automatically:

**`lang-set`** — detects and applies locale. Auto-added to `web` group when `autoload` is `true`.

```php
// Manual registration if autoload is disabled
Route::middleware('lang-set')->group(function () {
    // ...
});
```

**`lang-prefix`** — for URL-prefix-based localization (`/en/about`, `/ru/about`). Redirects to prefixed URL if prefix is missing.

```php
// routes/web.php
Route::prefix('{lang}')->middleware('lang-prefix')->group(function () {
    Route::get('/about', [AboutController::class, 'index'])->name('about');
});
```

Requires `Prefix` driver in your config:

```php
'get' => [
    \Flamix\Lang\Drivers\Prefix::class,
    \Flamix\Lang\Drivers\Cookies::class,
    // ...
],
```

### Bare-URL redirect

With prefix-based routing, unprefixed URLs (`/about`, `/checkout`) match nothing and would 404. Register pages through `lang_prefixes()` — with `prefix_fallback` on (default) it appends a bare `''` prefix, so the same group also registers unprefixed URLs and the `lang-prefix` middleware redirects them to the detected locale (query string preserved, so UTM tags survive):

```php
// routes/web.php — one loop registers /en/about, /ru/about, /ua/about AND /about
foreach (lang_prefixes() as $locale) {
    Route::group(['middleware' => 'lang-prefix', 'prefix' => $locale, 'as' => ($locale ?: 'bare').'.'], function () {
        Route::view('/about', 'about')->name('about');
    });
}
```

```
GET /about?utm_source=x  →  302  /en/about?utm_source=x
GET /api/nope            →  404  (only page routes redirect, never API or dashboards)
```

```php
// config/lang.php — turn off if the app doesn't want bare page URLs at all
'prefix_fallback' => env('LANG_PREFIX_FALLBACK', true),
```

There is no global catch-all: a URL that matches no route is an honest 404.

## Drivers

| Driver | Detects | Persists | Notes |
|--------|---------|----------|-------|
| `Cookies` | `lang` cookie | Forever cookie | Default, works for guests |
| `AuthUser` | `auth()->user()->lang` | Updates user model | Requires `lang` column |
| `Browser` | `Accept-Language` header | -- | Read-only, fallback |
| `Prefix` | URL first segment (`/en/...`) | -- | Read-only, use with `lang-prefix` middleware |

### Custom driver

```php
use Flamix\Lang\Drivers\AbstractDriver;
use Flamix\Lang\Drivers\Contracts\DetectInterface;
use Flamix\Lang\Drivers\Contracts\SetInterface;

class SessionDriver extends AbstractDriver implements DetectInterface, SetInterface
{
    public function detect(): ?string
    {
        $lang = session('lang');
        return $this->isAvailable($lang) ? $lang : null;
    }

    public function set(string $lang): mixed
    {
        session(['lang' => $lang]);
        return $lang;
    }
}
```

Add to config:

```php
'drivers' => [
    'get' => [SessionDriver::class, ...],
    'set' => [SessionDriver::class, ...],
],
```

`DetectInterface` — for detection only (like `Browser`). Add `SetInterface` if the driver can persist.
