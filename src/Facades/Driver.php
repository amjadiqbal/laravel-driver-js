<?php

namespace AmjadIqbal\DriverJS\Facades;

use Illuminate\Support\Facades\Facade;

class Driver extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'driverjs';
    }
}
