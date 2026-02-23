<p align="center">
  <img src="https://raw.githubusercontent.com/amjadiqbal/laravel-driver-js/main/assets/laravel-driver-js.png" alt="Laravel Driver.js" width="100%" />
</p>

<p align="center">
  <a href="https://github.com/amjadiqbal/laravel-driver-js/blob/main/LICENSE"><img alt="License" src="https://img.shields.io/badge/license-MIT-blue.svg"></a>
  <a href="https://packagist.org/packages/amjadiqbal/laravel-driver-js"><img alt="Packagist Version" src="https://img.shields.io/packagist/v/amjadiqbal/laravel-driver-js.svg"></a>
  <a href="https://packagist.org/packages/amjadiqbal/laravel-driver-js"><img alt="Downloads" src="https://img.shields.io/packagist/dt/amjadiqbal/laravel-driver-js.svg"></a>
  <a href="https://github.com/amjadiqbal/laravel-driver-js/actions/workflows/tests.yml"><img alt="CI" src="https://github.com/amjadiqbal/laravel-driver-js/actions/workflows/tests.yml/badge.svg"></a>
  <a href="https://github.com/amjadiqbal/laravel-driver-js/actions/workflows/pint.yml"><img alt="Code Style" src="https://github.com/amjadiqbal/laravel-driver-js/actions/workflows/pint.yml/badge.svg"></a>
  <a href="https://github.com/amjadiqbal/laravel-driver-js/actions/workflows/phpstan.yml"><img alt="Static Analysis" src="https://github.com/amjadiqbal/laravel-driver-js/actions/workflows/phpstan.yml/badge.svg"></a>
  <a href="https://codecov.io/gh/amjadiqbal/laravel-driver-js"><img alt="Coverage" src="https://codecov.io/gh/amjadiqbal/laravel-driver-js/branch/main/graph/badge.svg"></a>
  <img alt="PHP" src="https://img.shields.io/badge/PHP-%5E8.1-777BB4?logo=php">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-10%20%7C%2011-FF2D20?logo=laravel">
  <a href="https://driverjs.com"><img alt="Driver.js" src="https://img.shields.io/badge/Driver.js-1.3%2B-black"></a>
  <a href="https://pestphp.com"><img alt="Tests" src="https://img.shields.io/badge/Tests-Pest-2A5FE3"></a>
</p>

# amjadiqbal/laravel-driver-js

Professional Laravel wrapper for Driver.js (v1.3+). Easily create interactive product tours, feature highlights, and user onboarding flows using a fluent PHP API.

- SEO: Laravel product tours, interactive onboarding, feature highlights, guided walkthroughs, Driver.js for Laravel
- Website: https://driverjs.com

<p align="center">
  <a href="https://github.com/amjadiqbal/laravel-driver-js/issues">Issues</a> •
  <a href="#installation">Install</a> •
  <a href="#usage">Usage</a> •
  <a href="#methods-mapping">API</a> •
  <a href="#configuration">Config</a>
</p>

## Requirements

- PHP ^8.1
- Laravel 10 or 11
- Driver.js v1.3+

## Installation

```bash
composer require amjadiqbal/laravel-driver-js
php artisan vendor:publish --tag=driver-js-config
php artisan vendor:publish --tag=driver-js-views
```

## Features

- Fluent PHP API mirroring Driver.js options and actions
- Blade component for quick start
- Config publishing with sensible defaults and CDN control
- Callback wiring from PHP config to global JS functions
- Pest tests via Orchestra Testbench
- GitHub Actions CI and Laravel Pint code style

## Usage

### Fluent API

```php
use AmjadIqbal\DriverJS\Facades\Driver;

$tour = Driver::make()
    ->allowClose(true)
    ->overlayColor('rgba(0,0,0,0.6)')
    ->addStep('#selector', 'Title', 'Description')
    ->addStep('.cta', 'Get Started', 'Click here to begin')
    ->drive();

$json = $tour->toJson();
```

### Blade Component

```blade
<x-driver-js :steps="$steps" :config="['overlayColor' => 'rgba(0,0,0,0.6)']" />
```

Where `$steps` is an array:

```php
$steps = [
    [
        'element' => '#selector',
        'popover' => [
            'title' => 'Title',
            'description' => 'Description',
        ],
    ],
];
```

## Configuration

`config/driver-js.php`

```php
return [
    'cdn_js' => 'https://unpkg.com/driver.js/dist/driver.min.js',
    'cdn_css' => 'https://unpkg.com/driver.js/dist/driver.min.css',
    'allowClose' => true,
    'overlayColor' => 'rgba(0, 0, 0, 0.5)',
    'opacity' => 0.6,
];
```

You can override any of these values per-tour via the Blade `:config` prop or by constructing a builder with initial config: `Driver::make(['overlayColor' => '...'])`.

CDN keys (`cdn_js`, `cdn_css`) are used by the Blade component to load assets and are not forwarded to the Driver constructor.

## Methods Mapping

| Fluent Method            | Driver.js Option / Action     | Notes                                |
|--------------------------|-------------------------------|--------------------------------------|
| allowClose(bool)         | allowClose                    | Enable closing overlay               |
| overlayColor(string)     | overlayColor                  | RGBA or HEX                          |
| opacity(float)           | opacity                       | Overlay opacity                      |
| set(key, value)          | any config key                | Direct set                            |
| addStep(selector, title, description, options) | defineSteps -> step | Popover title/description            |
| defineSteps(array)       | defineSteps                   | Bulk steps                           |
| drive()                  | drive                         | Start tour                           |
| highlight(selector)      | highlight                     | Highlight element                    |
| moveNext()               | moveNext                      | Next step                            |
| movePrevious()           | movePrevious                  | Previous step                        |
| destroy()                | destroy                       | Destroy tour                         |
| onHighlighting(handler)  | onHighlighting callback       | Provide global callback name         |

### Blade Component Props

- `steps`: array of Driver.js steps (as shown above)
- `config`: array of overrides; merged with `config('driver-js')`

### Callbacks

- Provide callback names as strings, e.g. `onHighlighting('onHighlight')`
- Ensure a matching global function exists: `window.onHighlight = function (element) { ... }`
- The component resolves names to `window` functions before initializing Driver

### Step Options

- The `addStep` method supports an `options` array to pass step-level options supported by Driver.js (e.g., popover position, padding). These are forwarded as-is to the step object.

## Documentation

- API Docs: https://driverjs.com
- YouTube: Tutorials and walkthroughs on product tours and onboarding
- DEV Community: Implementation guides and tips

## CI & Code Style

- Tests: GitHub Actions workflow runs Pest on supported PHP versions
- Code Style: Laravel Pint workflow validates formatting (non-mutating with `--test`)
- Local commands:

```bash
composer test
composer lint
```

## License

MIT
