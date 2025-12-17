<?php

namespace Stesa\CloudlinkerClient\DTOs;

class Device
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $clientId = null,
        public readonly ?string $name = null,
        public readonly ?string $type = null,
        public readonly ?string $driver = null,
        public readonly ?string $connection = null,
        public readonly ?array $settings = null,
        public readonly ?string $status = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            clientId: $data['client_id'] ?? null,
            name: $data['name'] ?? null,
            type: $data['type'] ?? null,
            driver: $data['driver'] ?? null,
            connection: $data['connection'] ?? null,
            settings: $data['settings'] ?? null,
            status: $data['status'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'client_id' => $this->clientId,
            'name' => $this->name,
            'type' => $this->type,
            'driver' => $this->driver,
            'connection' => $this->connection,
            'settings' => $this->settings,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ], fn ($value) => $value !== null);
    }

    public function isPrinter(): bool
    {
        return $this->type === 'printer';
    }

    public function isScanner(): bool
    {
        return $this->type === 'scanner';
    }

    public function isScale(): bool
    {
        return $this->type === 'scale';
    }
}
