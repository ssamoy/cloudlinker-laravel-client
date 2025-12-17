<?php

namespace Stesa\CloudlinkerClient\Exceptions;

class ValidationException extends CloudlinkerException
{
    protected array $errors = [];

    public function __construct(string $message = 'Validation failed.', array $errors = [])
    {
        parent::__construct($message, 422, null, ['errors' => $errors]);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
