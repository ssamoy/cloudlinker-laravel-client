<?php

namespace Stesa\CloudlinkerClient\Resources;

use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\Concerns\DispatchesEvents;
use Stesa\CloudlinkerClient\DTOs\Client;
use Stesa\CloudlinkerClient\Events\ClientDeleted;

class ClientResource
{
    use DispatchesEvents;

    public function __construct(
        protected CloudlinkerClient $client
    ) {
    }

    /**
     * List all clients.
     *
     * @return array{data: Client[], meta: array, links: array}
     */
    public function list(?string $hostname = null, int $page = 1, int $perPage = 15): array
    {
        $data = array_filter([
            'hostname' => $hostname,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        $response = $this->client->post('clients/list', $data);

        return [
            'data' => array_map(
                fn (array $item) => Client::fromArray($item),
                $response['data'] ?? []
            ),
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ];
    }

    /**
     * Get all clients (auto-paginated).
     *
     * @return Client[]
     */
    public function all(?string $hostname = null): array
    {
        $clients = [];
        $page = 1;

        do {
            $response = $this->list($hostname, $page);
            $clients = array_merge($clients, $response['data']);
            $page++;
        } while (isset($response['meta']['last_page']) && $page <= $response['meta']['last_page']);

        return $clients;
    }

    /**
     * Delete a client.
     */
    public function delete(string $id): bool
    {
        $response = $this->client->post('clients/delete', ['id' => $id]);

        $success = ($response['success'] ?? false) === true;

        if ($success) {
            $this->dispatchEvent(new ClientDeleted($id));
        }

        return $success;
    }
}
