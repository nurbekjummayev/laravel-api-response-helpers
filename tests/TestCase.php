<?php

namespace Nurbekjummayev\ApiResponseHelper\Tests;

use Nurbekjummayev\ApiResponseHelper\ApiResponseHelperServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ApiResponseHelperServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
