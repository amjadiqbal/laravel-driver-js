<?php

use Illuminate\Support\Facades\Blade;

it('renders the blade component with required scripts', function () {
    $html = Blade::render('<x-driver-js :steps="$steps" />', [
        'steps' => [
            [
                'element' => '#selector',
                'popover' => ['title' => 'T', 'description' => 'D'],
            ],
        ],
    ]);

    expect($html)->toContain('driver.min.js');
    expect($html)->toContain('driver.min.css');
    expect($html)->toContain('driver.defineSteps');
    expect($html)->toContain('driver.drive');
});

it('merges global config with per-tour overrides', function () {
    config()->set('driver-js.overlayColor', 'rgba(1,1,1,0.1)');
    $html = Blade::render('<x-driver-js :steps="$steps" :config="$config" />', [
        'steps' => [
            [
                'element' => '#selector',
                'popover' => ['title' => 'T', 'description' => 'D'],
            ],
        ],
        'config' => ['overlayColor' => 'rgba(2,2,2,0.2)'],
    ]);

    expect($html)->toContain('rgba(2,2,2,0.2)');
    expect($html)->not()->toContain('rgba(1,1,1,0.1)');
});
