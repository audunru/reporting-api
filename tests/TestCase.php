<?php

namespace audunru\ReportingApi\Tests;

use audunru\ReportingApi\ReportingApiServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @SuppressWarnings("unused")
     */
    protected function getPackageProviders($app): array
    {
        return [ReportingApiServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.debug', env('APP_DEBUG') === 'true');
        $app['config']->set('app.key', substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 5)), 0, 32));
    }
}
