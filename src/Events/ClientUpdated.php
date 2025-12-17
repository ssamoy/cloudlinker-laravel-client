<?php

namespace Stesa\CloudlinkerClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stesa\CloudlinkerClient\DTOs\Client;

class ClientUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Client $client
    ) {
    }
}
