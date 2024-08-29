<?php

namespace Flamix\Lang;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Routing\Router;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(Router $router)
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

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
