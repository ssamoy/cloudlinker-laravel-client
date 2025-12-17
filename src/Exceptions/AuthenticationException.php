<?php

namespace Stesa\CloudlinkerClient\Exceptions;

class AuthenticationException extends CloudlinkerException
{
    public function __construct(string $message = 'Invalid Cloudlinker credentials. Please check your organisation ID and API key.')
    {
        parent::__construct($message, 401);
    }
}
