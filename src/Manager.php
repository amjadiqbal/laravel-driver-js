<?php

namespace AmjadIqbal\DriverJS;

class Manager
{
    public function make(array $config = []): Driver
    {
        return Driver::make($config);
    }
}
