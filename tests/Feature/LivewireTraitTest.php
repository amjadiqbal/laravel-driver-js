<?php

use AmjadIqbal\DriverJS\Livewire\StartsTours;

it('dispatches the driver-js:start browser event', function () {
    $component = new class
    {
        use StartsTours;

        /** @var array<int, array<string, mixed>> */
        public array $dispatched = [];

        public function dispatch(string $event, mixed ...$params): void
        {
            $this->dispatched[] = [$event, $params];
        }
    };

    $component->startTour('welcome', 2);

    expect($component->dispatched)->toBe([['driver-js:start', ['tour' => 'welcome', 'step' => 2]]]);
});
