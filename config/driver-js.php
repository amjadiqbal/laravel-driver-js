<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver.js assets
    |--------------------------------------------------------------------------
    |
    | The first <x-driver-js> on a page prints the Driver.js tags below plus
    | the package's small tour runtime (or put @driverJsAssets in your
    | layout's <head> yourself). The defaults load Driver.js 1.10.2 from
    | jsDelivr with Subresource Integrity hashes.
    |
    | Bundling Driver.js with Vite instead? Set "cdn" to false (the runtime
    | is still printed), `npm i driver.js`, and in resources/js/app.js:
    |
    |     import { driver } from 'driver.js';
    |     import 'driver.js/dist/driver.css';
    |     window.driver = { js: { driver } };
    |
    | If you change a URL, update its integrity hash or set it to null.
    |
    */

    'assets' => [
        'cdn' => true,
        'js' => 'https://cdn.jsdelivr.net/npm/driver.js@1.10.2/dist/driver.js.iife.js',
        'js_integrity' => 'sha384-5g481DgM/VdK19dNiL3kmTG/tz1fQriwUvSide6vc77uYGaXh0s5GLQCUN6dgI8n',
        'css' => 'https://cdn.jsdelivr.net/npm/driver.js@1.10.2/dist/driver.css',
        'css_integrity' => 'sha384-q84NOjxOvSzdVT3EjRGos6w4J4t7ysF+ueMkf5CCMevHRH2IkF+sEvNvofEU67dW',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Driver.js options
    |--------------------------------------------------------------------------
    |
    | Applied to every tour, then overridden by the tour's own options. Keys
    | are Driver.js 1.x option names: https://driverjs.com/docs/configuration
    |
    */

    'defaults' => [
        'showProgress' => true,
        'animate' => true,
        'allowClose' => true,
        'overlayOpacity' => 0.7,
        'stagePadding' => 8,
        'stageRadius' => 6,
    ],

];
