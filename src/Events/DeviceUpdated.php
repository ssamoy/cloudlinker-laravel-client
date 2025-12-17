<?php

namespace Stesa\CloudlinkerClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stesa\CloudlinkerClient\DTOs\Device;

class DeviceUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Device $device
    ) {
    }
}
