<?php

namespace Stesa\CloudlinkerClient\Exceptions;

class NotFoundException extends CloudlinkerException
{
    public function __construct(string $message = 'Resource not found.')
    {
        parent::__construct($message, 404);
    }
}
