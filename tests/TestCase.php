<?php

namespace AmjadIqbal\DriverJS\Tests;

use AmjadIqbal\DriverJS\DriverJSServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DriverJSServiceProvider::class,
        ];
    }
}
