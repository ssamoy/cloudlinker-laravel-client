<?php

namespace Stesa\CloudlinkerClient\Tests\Feature;

use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\Facades\Cloudlinker;
use Stesa\CloudlinkerClient\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_cloudlinker_client_is_bound_to_container(): void
    {
        $client = $this->app->make(CloudlinkerClient::class);

        $this->assertInstanceOf(CloudlinkerClient::class, $client);
    }

    public function test_cloudlinker_is_singleton(): void
    {
        $client1 = $this->app->make(CloudlinkerClient::class);
        $client2 = $this->app->make(CloudlinkerClient::class);

        $this->assertSame($client1, $client2);
    }

    public function test_facade_resolves_to_client(): void
    {
        $this->assertInstanceOf(CloudlinkerClient::class, Cloudlinker::getFacadeRoot());
    }

    public function test_config_is_merged(): void
    {
        $this->assertEquals('test-org', config('cloudlinker.organisation_id'));
        $this->assertEquals('test-key', config('cloudlinker.api_key'));
        $this->assertEquals('https://cloudlinker.eu/api', config('cloudlinker.base_url'));
        $this->assertEquals(30, config('cloudlinker.timeout'));
    }
}
