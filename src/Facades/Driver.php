<?php

namespace AmjadIqbal\DriverJS\Facades;

use AmjadIqbal\DriverJS\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \AmjadIqbal\DriverJS\Driver make(array<string, mixed> $config = [])
 * @method static \AmjadIqbal\DriverJS\Driver tour(string $name)
 * @method static \Illuminate\Support\HtmlString assets()
 *
 * @see Manager
 */
class Driver extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
