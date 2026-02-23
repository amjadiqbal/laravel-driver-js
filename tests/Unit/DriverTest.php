<?php

use AmjadIqbal\DriverJS\Driver;

it('can add a tour step with a valid selector', function () {
    $driver = Driver::make()
        ->addStep('#selector', 'Title', 'Description');

    $data = $driver->toArray();
    expect($data['steps'])->toBeArray();
    expect($data['steps'][0]['element'])->toBe('#selector');
    expect($data['steps'][0]['popover']['title'])->toBe('Title');
    expect($data['steps'][0]['popover']['description'])->toBe('Description');
});

it('generates the correct JSON configuration for the frontend', function () {
    $driver = Driver::make()
        ->overlayColor('rgba(0,0,0,0.7)')
        ->allowClose(false)
        ->addStep('#a', 'A', 'B')
        ->drive();

    $json = $driver->toJson();
    $decoded = json_decode($json, true);

    expect($decoded['config']['overlayColor'])->toBe('rgba(0,0,0,0.7)');
    expect($decoded['config']['allowClose'])->toBeFalse();
    expect($decoded['steps'])->toHaveCount(1);
    expect($decoded['action']['type'])->toBe('drive');
});
