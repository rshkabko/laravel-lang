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

### Bare-URL fallback

With prefix-based routing, unprefixed URLs (`/about`, `/checkout`) don't match any route and 404. The fallback (enabled by default) redirects them to the detected locale (query string preserved, so UTM tags survive):

```php
// config/lang.php — disable if the app doesn't use prefix routing
'prefix_fallback' => env('LANG_PREFIX_FALLBACK', true),
```

```
GET /checkout?utm_source=x  →  302  /en/checkout?utm_source=x
```

No more hand-written redirect routes in `routes/web.php`. URLs that already carry a valid prefix but match nothing stay a plain 404.

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
