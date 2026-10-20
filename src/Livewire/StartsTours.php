<?php

namespace AmjadIqbal\DriverJS\Livewire;

/**
 * Use in a Livewire component to start a tour rendered on the page by
 * <x-driver-js>, e.g. after a save: $this->startTour('welcome').
 *
 * Requires Livewire 3 or 4 (it calls the component's own dispatch()).
 */
trait StartsTours
{
    public function startTour(string $name, int $step = 0): void
    {
        $this->dispatch('driver-js:start', tour: $name, step: $step);
    }
}
