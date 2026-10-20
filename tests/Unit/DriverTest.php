<?php

use AmjadIqbal\DriverJS\Driver;

it('builds a step with Driver.js 1.x keys', function () {
    $data = Driver::make()
        ->step('#billing', 'Billing', 'Manage your plan here.', ['side' => 'left', 'align' => 'start'])
        ->toArray();

    expect($data['steps'][0])->toBe([
        'element' => '#billing',
        'popover' => [
            'title' => 'Billing',
            'description' => 'Manage your plan here.',
            'side' => 'left',
            'align' => 'start',
        ],
    ]);
});

it('allows a step without an element for a centred popover', function () {
    $step = Driver::make()->step(null, 'Welcome')->toArray()['steps'][0];

    expect($step)->not->toHaveKey('element')
        ->and($step['popover'])->toBe(['title' => 'Welcome']);
});

it('maps fluent options to real Driver.js option names', function () {
    $config = Driver::make()
        ->animate(false)
        ->overlayColor('#0b1020')
        ->overlayOpacity(0.5)
        ->smoothScroll()
        ->allowClose(false)
        ->allowKeyboardControl(false)
        ->overlayClickBehavior('nextStep')
        ->stagePadding(4)
        ->stageRadius(10)
        ->disableActiveInteraction()
        ->popoverClass('my-theme')
        ->popoverOffset(12)
        ->showProgress(true, '{{current}} / {{total}}')
        ->showButtons(['next', 'previous'])
        ->disableButtons(['previous'])
        ->buttonLabels('Next →', '← Back', 'Got it')
        ->toArray()['config'];

    expect($config)->toBe([
        'animate' => false,
        'overlayColor' => '#0b1020',
        'overlayOpacity' => 0.5,
        'smoothScroll' => true,
        'allowClose' => false,
        'allowKeyboardControl' => false,
        'overlayClickBehavior' => 'nextStep',
        'stagePadding' => 4,
        'stageRadius' => 10,
        'disableActiveInteraction' => true,
        'popoverClass' => 'my-theme',
        'popoverOffset' => 12,
        'showProgress' => true,
        'progressText' => '{{current}} / {{total}}',
        'showButtons' => ['next', 'previous'],
        'disableButtons' => ['previous'],
        'nextBtnText' => 'Next →',
        'prevBtnText' => '← Back',
        'doneBtnText' => 'Got it',
    ]);
});

it('keeps the v0.1 method names working', function () {
    $data = Driver::make()
        ->addStep('#a', 'A', 'B')
        ->opacity(0.4)
        ->onHighlighting('started')
        ->toArray();

    expect($data['steps'][0]['element'])->toBe('#a')
        ->and($data['config']['overlayOpacity'])->toBe(0.4)
        ->and($data['hooks'])->toBe(['onHighlightStarted' => 'started']);

    expect(Driver::make()->defineSteps([['element' => '#x']])->toArray()['steps'])->toBe([['element' => '#x']]);
});

it('records tour name, once key and auto start', function () {
    $data = Driver::make()->name('welcome')->once('welcome-v1')->manual()->toArray();

    expect($data['name'])->toBe('welcome')
        ->and($data['once'])->toBe('welcome-v1')
        ->and($data['autoStart'])->toBeFalse();
});

it('stores a single highlight instead of steps', function () {
    $data = Driver::make()->highlight('#new', 'New!', 'Try exports.')->toArray();

    expect($data['highlight'])->toBe(['element' => '#new', 'popover' => ['title' => 'New!', 'description' => 'Try exports.']])
        ->and($data['steps'])->toBe([]);
});

it('wires hooks by function name', function () {
    expect(Driver::make()->on('onDestroyed', 'App.tourDone')->toArray()['hooks'])
        ->toBe(['onDestroyed' => 'App.tourDone']);
});

it('rejects invalid values early', function () {
    expect(fn () => Driver::make()->on('onClick', 'x'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Driver::make()->step('#a', 'A', '', ['side' => 'middle']))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Driver::make()->step('#a', 'A', '', ['align' => 'left']))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Driver::make()->showButtons(['done']))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Driver::make()->overlayClickBehavior('open'))->toThrow(InvalidArgumentException::class);
});

it('serialises to JSON', function () {
    $tour = Driver::make()->name('t')->step('#a', 'A');

    expect(json_decode($tour->toJson(), true))->toBe($tour->toArray())
        ->and(json_encode($tour))->toBe($tour->toJson());
});
