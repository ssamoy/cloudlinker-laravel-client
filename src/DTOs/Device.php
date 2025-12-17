<?php

namespace Stesa\CloudlinkerClient\DTOs;

class Device
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $clientId = null,
        public readonly ?string $name = null,
        public readonly ?string $hardwarePath = null,
        public readonly ?array $additionalInfo = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            clientId: $data['client_id'] ?? null,
            name: $data['name'] ?? null,
            hardwarePath: $data['hardware_path'] ?? null,
            additionalInfo: $data['additional_info'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'client_id' => $this->clientId,
            'name' => $this->name,
            'hardware_path' => $this->hardwarePath,
            'additional_info' => $this->additionalInfo,
        ], fn ($value) => $value !== null);
    }
}
