<?php

namespace AmjadIqbal\DriverJS;

use AmjadIqbal\DriverJS\View\Components\DriverJs;
use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DriverJSServiceProvider extends ServiceProvider
{
    public const SUPPORT_URL = 'https://amjad.com.pk/contact?utm_source=artisan&utm_medium=about&utm_campaign=laravel-driver-js';

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/driver-js.php', 'driver-js');

        $this->app->scoped(Manager::class);
        $this->app->alias(Manager::class, 'driverjs');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'driver-js');

        Blade::component('driver-js', DriverJs::class);
        Blade::directive('driverJsAssets', fn () => '<?php echo app(\AmjadIqbal\DriverJS\Manager::class)->assets(); ?>');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/driver-js.php' => config_path('driver-js.php'),
            ], 'driver-js-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/driver-js'),
            ], 'driver-js-views');
        }

        AboutCommand::add('Driver.js tours', fn () => [
            'Version' => InstalledVersions::isInstalled('amjadiqbal/laravel-driver-js')
                ? (string) InstalledVersions::getPrettyVersion('amjadiqbal/laravel-driver-js')
                : 'unknown',
            'Support' => self::SUPPORT_URL,
        ]);
    }
}
