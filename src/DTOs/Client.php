<?php

namespace Stesa\CloudlinkerClient\DTOs;

class Client
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $hostname = null,
        public readonly ?string $description = null,
        public readonly ?string $ipAddress = null,
        public readonly ?string $lastSeen = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            hostname: $data['hostname'] ?? null,
            description: $data['description'] ?? null,
            ipAddress: $data['ip_address'] ?? null,
            lastSeen: $data['last_seen'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'hostname' => $this->hostname,
            'description' => $this->description,
            'ip_address' => $this->ipAddress,
            'last_seen' => $this->lastSeen,
        ], fn ($value) => $value !== null);
    }

    public function isOnline(): bool
    {
        // Client is considered online if last_seen is recent (within last 5 minutes)
        if (empty($this->lastSeen)) {
            return false;
        }

        try {
            $lastSeen = new \DateTime($this->lastSeen);
            $now = new \DateTime();
            $diff = $now->getTimestamp() - $lastSeen->getTimestamp();

            return $diff < 300; // 5 minutes
        } catch (\Exception) {
            return false;
        }
    }
}
