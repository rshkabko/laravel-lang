<?php

namespace Flamix\Lang;

use Flamix\Lang\Middleware\SetLang;
use Flamix\Lang\Middleware\PrefixLang;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(Router $router)
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $router->aliasMiddleware('lang-set', SetLang::class);
        $router->aliasMiddleware('lang-prefix', PrefixLang::class);

        if (config('lang.autoload', true)) {
            $this->app->booted(function () use ($router) {
                $router->pushMiddlewareToGroup('web', 'lang-set');
            });
        }

        // Redirect bare URLs (/about) to the locale prefix (/en/about).
        // Registered after app routes boot so the catch-all stays last.
        if (config('lang.prefix_fallback', true) && !$this->app->routesAreCached()) {
            $this->app->booted(function () {
                Route::fallback([\Flamix\Lang\Controllers\LangController::class, 'redirectToPrefix'])->middleware('web');
            });
        }

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lang.php', 'lang');
    }

    protected function bootForConsole()
    {
        $this->publishes([__DIR__ . '/../config' => config_path()], 'config');
    }
}
