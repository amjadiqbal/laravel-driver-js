<?php

use AmjadIqbal\DriverJS\Driver;
use AmjadIqbal\DriverJS\Facades\Driver as DriverFacade;
use AmjadIqbal\DriverJS\Manager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;

function tourDefinition(string $html): array
{
    preg_match("/register\(JSON\.parse\('(.*)'\)\)/", $html, $m);

    expect($m)->not->toBeEmpty();

    return json_decode(json_decode('"'.$m[1].'"'), true);
}

it('prints Driver.js 1.x assets with integrity hashes and the runtime', function () {
    $html = Blade::render('<x-driver-js :tour="$tour" />', ['tour' => Driver::make()->step('#a', 'A')]);

    expect($html)
        ->toContain('https://cdn.jsdelivr.net/npm/driver.js@1.10.2/dist/driver.js.iife.js')
        ->toContain('https://cdn.jsdelivr.net/npm/driver.js@1.10.2/dist/driver.css')
        ->toContain('integrity="sha384-')
        ->toContain('window.LaravelDriverJs = {')
        ->toContain('window.driver.js.driver')
        ->not->toContain('driver.min.js')
        ->not->toContain('defineSteps');
});

it('prints the assets only once per request', function () {
    $html = Blade::render('<x-driver-js name="one" :steps="[]" /><x-driver-js name="two" :steps="[]" />');

    expect(substr_count($html, 'driver.js.iife.js'))->toBe(1)
        ->and(substr_count($html, 'LaravelDriverJs.register('))->toBe(2);
});

it('lets @driverJsAssets place the assets, and the component then skips them', function () {
    $html = Blade::render('@driverJsAssets<x-driver-js :steps="[]" />');

    expect(substr_count($html, 'driver.css'))->toBe(1);
});

it('skips the CDN tags but keeps the runtime when cdn is false', function () {
    config()->set('driver-js.assets.cdn', false);

    $html = Blade::render('<x-driver-js :steps="[]" />');

    expect($html)->not->toContain('jsdelivr')->toContain('window.LaravelDriverJs = {');
});

it('registers a tour definition with defaults merged under the tour options', function () {
    config()->set('driver-js.defaults', ['showProgress' => true, 'overlayColor' => 'red']);

    $tour = DriverFacade::tour('welcome')->once('welcome-v1')->overlayColor('blue')->step('#a', 'A', 'B');

    $definition = tourDefinition(Blade::render('<x-driver-js :tour="$tour" />', ['tour' => $tour]));

    expect($definition['name'])->toBe('welcome')
        ->and($definition['once'])->toBe('welcome-v1')
        ->and($definition['autoStart'])->toBeTrue()
        ->and($definition['config'])->toBe(['showProgress' => true, 'overlayColor' => 'blue'])
        ->and($definition['steps'][0]['element'])->toBe('#a');
});

it('accepts raw steps and config props, as in v0.1', function () {
    config()->set('driver-js.defaults.overlayColor', 'rgba(1,1,1,0.1)');

    $definition = tourDefinition(Blade::render('<x-driver-js :steps="$steps" :config="$config" name="raw" :auto-start="false" />', [
        'steps' => [['element' => '#s', 'popover' => ['title' => 'T']]],
        'config' => ['overlayColor' => 'rgba(2,2,2,0.2)'],
    ]));

    expect($definition['name'])->toBe('raw')
        ->and($definition['autoStart'])->toBeFalse()
        ->and($definition['config']['overlayColor'])->toBe('rgba(2,2,2,0.2)')
        ->and($definition['steps'][0]['element'])->toBe('#s');
});

it('escapes tour text so it cannot break out of the script tag', function () {
    $html = Blade::render('<x-driver-js :tour="$tour" />', [
        'tour' => Driver::make()->step('#a', '</script><script>alert(1)</script>'),
    ]);

    expect($html)->not->toContain('</script><script>alert(1)');
});

it('resolves the facade and alias to one scoped manager', function () {
    expect(app('driverjs'))->toBe(app(Manager::class))
        ->and(DriverFacade::tour('x')->getName())->toBe('x');
});

it('adds a passive line to php artisan about', function () {
    Artisan::call('about');

    expect(Artisan::output())
        ->toContain('Driver.js tours')
        ->toContain('amjad.com.pk/contact?utm_source=artisan&utm_medium=about&utm_campaign=laravel-driver-js');
});
