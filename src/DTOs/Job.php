<?php

namespace Stesa\CloudlinkerClient\DTOs;

class Job
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $deviceId = null,
        public readonly ?string $type = null,
        public readonly ?string $status = null,
        public readonly ?string $source = null,
        public readonly ?array $options = null,
        public readonly ?array $result = null,
        public readonly ?string $error = null,
        public readonly ?string $startedAt = null,
        public readonly ?string $completedAt = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            deviceId: $data['device_id'] ?? null,
            type: $data['type'] ?? null,
            status: $data['status'] ?? null,
            source: $data['source'] ?? null,
            options: $data['options'] ?? null,
            result: $data['result'] ?? null,
            error: $data['error'] ?? null,
            startedAt: $data['started_at'] ?? null,
            completedAt: $data['completed_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'device_id' => $this->deviceId,
            'type' => $this->type,
            'status' => $this->status,
            'source' => $this->source,
            'options' => $this->options,
            'result' => $this->result,
            'error' => $this->error,
            'started_at' => $this->startedAt,
            'completed_at' => $this->completedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ], fn ($value) => $value !== null);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
