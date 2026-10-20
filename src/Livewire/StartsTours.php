<?php

namespace AmjadIqbal\DriverJS\Livewire;

/**
 * Use in a Livewire component to start a tour rendered on the page by
 * <x-driver-js>, e.g. after a save: $this->startTour('welcome').
 *
 * Calls the Livewire component's own dispatch() (tested with Livewire 4.4.7).
 */
trait StartsTours
{
    public function startTour(string $name, int $step = 0): void
    {
        $this->dispatch('driver-js:start', tour: $name, step: $step);
    }
}
