<?php

namespace Stesa\CloudlinkerClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $deviceId
    ) {
    }
}
