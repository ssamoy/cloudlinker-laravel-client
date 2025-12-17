<?php

namespace Stesa\CloudlinkerClient\Resources;

use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\DTOs\Device;
use Stesa\CloudlinkerClient\Events\DeviceDeleted;

class DeviceResource
{
    public function __construct(
        protected CloudlinkerClient $client
    ) {
    }

    /**
     * List devices for a client.
     *
     * @return array{data: Device[], meta: array, links: array}
     */
    public function list(string $clientId, int $page = 1, int $perPage = 15): array
    {
        $response = $this->client->post('devices/list', [
            'client_id' => $clientId,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return [
            'data' => array_map(
                fn (array $item) => Device::fromArray($item),
                $response['data'] ?? []
            ),
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ];
    }

    /**
     * Get all devices for a client (auto-paginated).
     *
     * @return Device[]
     */
    public function all(string $clientId): array
    {
        $devices = [];
        $page = 1;

        do {
            $response = $this->list($clientId, $page);
            $devices = array_merge($devices, $response['data']);
            $page++;
        } while (isset($response['meta']['last_page']) && $page <= $response['meta']['last_page']);

        return $devices;
    }

    /**
     * Delete a device.
     */
    public function delete(string $id): bool
    {
        $response = $this->client->post('devices/delete', ['id' => $id]);

        $success = ($response['success'] ?? false) === true;

        if ($success) {
            event(new DeviceDeleted($id));
        }

        return $success;
    }
}
