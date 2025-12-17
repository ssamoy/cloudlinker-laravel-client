<?php

namespace Stesa\CloudlinkerClient\Exceptions;

class RateLimitException extends CloudlinkerException
{
    protected int $retryAfter;

    public function __construct(string $message = 'Rate limit exceeded.', int $retryAfter = 60)
    {
        parent::__construct($message, 429);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
