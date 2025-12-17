<?php

namespace Stesa\CloudlinkerClient\Exceptions;

use Exception;

class CloudlinkerException extends Exception
{
    protected array $context = [];

    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public static function fromResponse(array $response, int $statusCode): self
    {
        $message = $response['message'] ?? $response['error'] ?? 'Unknown Cloudlinker API error';

        return new self($message, $statusCode, null, $response);
    }
}
