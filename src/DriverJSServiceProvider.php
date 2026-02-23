<?php

namespace AmjadIqbal\DriverJS;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DriverJSServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/driver-js.php', 'driver-js');
        $this->app->singleton('driverjs', function () {
            return new Manager;
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'driver-js');
        Blade::component('driver-js', \AmjadIqbal\DriverJS\View\Components\DriverJs::class);

        $this->publishes([
            __DIR__.'/../config/driver-js.php' => config_path('driver-js.php'),
        ], 'driver-js-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/driver-js'),
        ], 'driver-js-views');
    }
}
