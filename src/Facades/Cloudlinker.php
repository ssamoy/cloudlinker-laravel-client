<?php

namespace Stesa\CloudlinkerClient\Facades;

use Illuminate\Support\Facades\Facade;
use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\Resources\ClientResource;
use Stesa\CloudlinkerClient\Resources\DeviceResource;
use Stesa\CloudlinkerClient\Resources\JobResource;

/**
 * @method static bool test()
 * @method static ClientResource clients()
 * @method static DeviceResource devices()
 * @method static JobResource jobs()
 *
 * @see \Stesa\CloudlinkerClient\CloudlinkerClient
 */
class Cloudlinker extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CloudlinkerClient::class;
    }
}
