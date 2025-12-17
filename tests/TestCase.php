<?php

namespace Stesa\CloudlinkerClient\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Stesa\CloudlinkerClient\CloudlinkerServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            CloudlinkerServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Cloudlinker' => \Stesa\CloudlinkerClient\Facades\Cloudlinker::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('cloudlinker.organisation_id', 'test-org');
        $app['config']->set('cloudlinker.api_key', 'test-key');
        $app['config']->set('cloudlinker.base_url', 'https://cloudlinker.eu/api');
        $app['config']->set('cloudlinker.timeout', 30);
    }
}
