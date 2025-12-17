<?php

namespace Stesa\CloudlinkerClient\Exceptions;

class QuotaExceededException extends CloudlinkerException
{
    public function __construct(string $message = 'Quota exceeded.')
    {
        parent::__construct($message, 400);
    }
}
