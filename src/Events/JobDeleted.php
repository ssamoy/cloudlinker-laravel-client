<?php

namespace Stesa\CloudlinkerClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $jobId
    ) {
    }
}
