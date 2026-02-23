<?php

namespace AmjadIqbal\DriverJS\View\Components;

use Illuminate\View\Component;

class DriverJs extends Component
{
    public array $steps;
    public array $config;

    public function __construct(array $steps = [], array $config = [])
    {
        $this->steps = $steps;
        $this->config = $config;
    }

    public function render()
    {
        return view('driver-js::components.driver-js');
    }
}
