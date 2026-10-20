<?php

namespace AmjadIqbal\DriverJS\View\Components;

use AmjadIqbal\DriverJS\Driver;
use AmjadIqbal\DriverJS\Manager;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DriverJs extends Component
{
    /** @var array<string, mixed> */
    public array $definition;

    /**
     * @param  Driver|array<string, mixed>|null  $tour  a Driver builder or its toArray() output
     * @param  array<int, array<string, mixed>>  $steps  raw Driver.js steps, when not passing $tour
     * @param  array<string, mixed>  $config  Driver.js options, merged over config('driver-js.defaults')
     */
    public function __construct(
        Driver|array|null $tour = null,
        array $steps = [],
        array $config = [],
        ?string $name = null,
        ?string $once = null,
        ?bool $autoStart = null,
    ) {
        $definition = $tour instanceof Driver ? $tour->toArray() : ($tour ?? []);

        $definition = array_merge([
            'name' => 'default',
            'config' => [],
            'hooks' => [],
            'steps' => [],
            'highlight' => null,
            'once' => null,
            'autoStart' => true,
        ], $definition);

        if ($steps !== []) {
            $definition['steps'] = array_values($steps);
        }

        $definition['config'] = array_merge(
            (array) config('driver-js.defaults', []),
            $definition['config'],
            $config,
        );

        $definition['name'] = $name ?? $definition['name'];
        $definition['once'] = $once ?? $definition['once'];
        $definition['autoStart'] = $autoStart ?? $definition['autoStart'];

        $this->definition = $definition;
    }

    public function assets(): string
    {
        return (string) app(Manager::class)->assets();
    }

    public function render(): View
    {
        return view('driver-js::components.driver-js');
    }
}
