# Changelog

All notable changes to `amjadiqbal/laravel-driver-js` are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses
[Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-10-21

Rebuilt against the real Driver.js 1.x API. v0.1.0 did not work with any current Driver.js: its default
CDN URL (`driver.js/dist/driver.min.js`) returns 404 and it called `new Driver()` / `defineSteps()`,
which Driver.js 1.x does not have. It also could not be installed on Laravel 12 or 13.

### Added

- Laravel 12 and 13 support (PHP 8.3+).
- Named tours: `Driver::tour('welcome')`, started from a button (`data-driver-start="welcome"`), Alpine
  (`$dispatch('driver-js:start', { tour: 'welcome' })`) or Livewire (`$this->dispatch(...)` or the
  `StartsTours` trait).
- `once('welcome-v1')`: auto-start a tour only the first time a visitor sees it, remembered in
  `localStorage`. Change the key to show an updated tour again.
- `manual()` / `autoStart(false)` to register a tour without starting it.
- `highlight()` for a single element instead of a tour.
- Fluent methods for every main Driver.js 1.x option (`overlayOpacity`, `overlayClickBehavior`,
  `stagePadding`, `stageRadius`, `showProgress`, `showButtons`, `buttonLabels`, ...) with validation of
  sides, alignments, buttons and hook names.
- `on($hook, 'window.functionName')` for the ten Driver.js hooks, resolved in the browser.
- `@driverJsAssets` directive; assets are printed once per request, with a CSP nonce when
  `Vite::useCspNonce()` is set.
- Driver.js 1.10.2 loaded from jsDelivr with Subresource Integrity hashes; `assets.cdn = false` to bundle
  it yourself.
- Works with Livewire `wire:navigate`.
- A "Driver.js tours" section in `php artisan about`.

### Changed

- Driver.js options moved under `defaults` in `config/driver-js.php`; CDN settings under `assets`.
- Tour data is escaped with `Js::from()` so tour text cannot break out of the `<script>` tag.
- `addStep()`, `defineSteps()`, `opacity()` and `onHighlighting()` still work but are deprecated in favour
  of `step()`, `steps()`, `overlayOpacity()` and `on('onHighlightStarted', ...)`.

### Removed

- `drive()`, `destroy()`, `moveNext()` and `movePrevious()` on the PHP builder. They recorded an action
  that the Blade component never read. Control a running tour from JavaScript with
  `LaravelDriverJs.active()`.
- Laravel 10 and 11, PHP 8.2.

## [0.1.0] - 2026-02-23

- Initial release.

[1.0.0]: https://github.com/amjadiqbal/laravel-driver-js/compare/v0.1.0...v1.0.0
[0.1.0]: https://github.com/amjadiqbal/laravel-driver-js/releases/tag/v0.1.0
