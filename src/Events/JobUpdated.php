<?php

namespace Stesa\CloudlinkerClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stesa\CloudlinkerClient\DTOs\Job;

class JobUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Job $job
    ) {
    }
}
