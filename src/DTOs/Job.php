<?php

namespace Stesa\CloudlinkerClient\DTOs;

class Job
{
    // Job type constants
    public const TYPE_PRINT = 1;
    public const TYPE_HTTP_COMMAND = 2;

    // Status constants
    public const STATUS_CREATED = 1;
    public const STATUS_LAUNCHED = 2;
    public const STATUS_PENDING = 3;
    public const STATUS_COMPLETED = 4;
    public const STATUS_FAILED = 5;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $clientId = null,
        public readonly ?string $deviceId = null,
        public readonly ?string $deviceName = null,
        public readonly ?int $jobType = null,
        public readonly ?int $statusCode = null,
        public readonly ?array $payload = null,
        public readonly ?array $result = null,
        public readonly ?string $error = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        // Parse payload if it's a JSON string
        $payload = $data['payload'] ?? null;
        if (is_string($payload) && !empty($payload)) {
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        // Parse result if it's a JSON string
        $result = $data['result'] ?? null;
        if (is_string($result) && !empty($result)) {
            $decoded = json_decode($result, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $result = $decoded;
            }
        }

        return new self(
            id: $data['id'] ?? null,
            clientId: $data['client_id'] ?? null,
            deviceId: $data['device_id'] ?? null,
            deviceName: $data['device_name'] ?? null,
            jobType: isset($data['job_type']) ? (int) $data['job_type'] : null,
            statusCode: isset($data['status']) ? (int) $data['status'] : null,
            payload: is_array($payload) ? $payload : null,
            result: is_array($result) ? $result : null,
            error: $data['error'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'client_id' => $this->clientId,
            'device_id' => $this->deviceId,
            'device_name' => $this->deviceName,
            'job_type' => $this->jobType,
            'status' => $this->statusCode,
            'payload' => $this->payload,
            'result' => $this->result,
            'error' => $this->error,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ], fn ($value) => $value !== null);
    }

    /**
     * Get the status as a human-readable string.
     */
    public function getStatusName(): string
    {
        return match ($this->statusCode) {
            self::STATUS_CREATED => 'created',
            self::STATUS_LAUNCHED => 'launched',
            self::STATUS_PENDING => 'pending',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_FAILED => 'failed',
            default => 'unknown',
        };
    }

    /**
     * Get the job type as a human-readable string.
     */
    public function getTypeName(): string
    {
        return match ($this->jobType) {
            self::TYPE_PRINT => 'print',
            self::TYPE_HTTP_COMMAND => 'http_command',
            default => 'unknown',
        };
    }

    public function isCreated(): bool
    {
        return $this->statusCode === self::STATUS_CREATED;
    }

    public function isLaunched(): bool
    {
        return $this->statusCode === self::STATUS_LAUNCHED;
    }

    public function isPending(): bool
    {
        return $this->statusCode === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->statusCode === self::STATUS_LAUNCHED || $this->statusCode === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->statusCode === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->statusCode === self::STATUS_FAILED;
    }

    /**
     * Check if this is an HTTP_COMMAND job.
     */
    public function isHttpCommand(): bool
    {
        return $this->jobType === self::TYPE_HTTP_COMMAND;
    }

    /**
     * Check if this is a print job.
     */
    public function isPrintJob(): bool
    {
        return $this->jobType === self::TYPE_PRINT;
    }

    /**
     * Get the HTTP status code from the result (for HTTP_COMMAND jobs).
     */
    public function getHttpStatus(): ?int
    {
        return $this->result['http_status'] ?? null;
    }

    /**
     * Get the HTTP response body from the result (for HTTP_COMMAND jobs).
     */
    public function getHttpResult(): ?string
    {
        return $this->result['http_result'] ?? null;
    }

    /**
     * Get the webhook HTTP status code from the result (for HTTP_COMMAND jobs with callback).
     */
    public function getWebhookHttpStatus(): ?int
    {
        return $this->result['webhook_http_status'] ?? null;
    }

    /**
     * Get the webhook HTTP response from the result (for HTTP_COMMAND jobs with callback).
     */
    public function getWebhookHttpResult(): ?string
    {
        return $this->result['webhook_http_result'] ?? null;
    }

    /**
     * Check if the HTTP request was successful (status 2xx).
     */
    public function isHttpSuccess(): bool
    {
        $status = $this->getHttpStatus();

        return $status !== null && $status >= 200 && $status < 300;
    }
}
