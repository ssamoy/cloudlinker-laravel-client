<?php

namespace Stesa\CloudlinkerClient\DTOs;

class Client
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $hostname = null,
        public readonly ?string $status = null,
        public readonly ?string $lastSeen = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            hostname: $data['hostname'] ?? null,
            status: $data['status'] ?? null,
            lastSeen: $data['last_seen'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'hostname' => $this->hostname,
            'status' => $this->status,
            'last_seen' => $this->lastSeen,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ], fn ($value) => $value !== null);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }
}
