<?php

namespace Stesa\CloudlinkerClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Stesa\CloudlinkerClient\Exceptions\AuthenticationException;
use Stesa\CloudlinkerClient\Exceptions\CloudlinkerException;
use Stesa\CloudlinkerClient\Exceptions\NotFoundException;
use Stesa\CloudlinkerClient\Exceptions\RateLimitException;
use Stesa\CloudlinkerClient\Exceptions\ValidationException;
use Stesa\CloudlinkerClient\Resources\ClientResource;
use Stesa\CloudlinkerClient\Resources\DeviceResource;
use Stesa\CloudlinkerClient\Resources\JobResource;

class CloudlinkerClient
{
    protected Client $httpClient;

    protected string $baseUrl;

    protected string $organisationId;

    protected string $apiKey;

    protected int $timeout;

    protected ?ClientResource $clientResource = null;

    protected ?DeviceResource $deviceResource = null;

    protected ?JobResource $jobResource = null;

    public function __construct(?string $organisationId = null, ?string $apiKey = null, ?string $baseUrl = null, ?int $timeout = null)
    {
        $this->organisationId = $organisationId ?? $this->getConfig('cloudlinker.organisation_id', '');
        $this->apiKey = $apiKey ?? $this->getConfig('cloudlinker.api_key', '');
        $this->baseUrl = rtrim($baseUrl ?? $this->getConfig('cloudlinker.base_url', 'https://cloudlinker.eu/api'), '/');
        $this->timeout = $timeout ?? $this->getConfig('cloudlinker.timeout', 30);

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl . '/',
            'timeout' => $this->timeout,
            'auth' => [$this->organisationId, $this->apiKey],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Get a configuration value, with Laravel fallback support.
     */
    protected function getConfig(string $key, mixed $default = null): mixed
    {
        // Check if Laravel's config helper is available
        if (function_exists('config') && function_exists('app')) {
            try {
                return config($key, $default);
            } catch (\Throwable) {
                // Laravel not fully booted, use default
            }
        }

        return $default;
    }

    /**
     * Test the API connection.
     *
     * @throws CloudlinkerException
     */
    public function test(): bool
    {
        $response = $this->get('test');

        // API returns organization_id on success
        return isset($response['organization_id']) && !empty($response['organization_id']);
    }

    /**
     * Get the clients resource.
     */
    public function clients(): ClientResource
    {
        if ($this->clientResource === null) {
            $this->clientResource = new ClientResource($this);
        }

        return $this->clientResource;
    }

    /**
     * Get the devices resource.
     */
    public function devices(): DeviceResource
    {
        if ($this->deviceResource === null) {
            $this->deviceResource = new DeviceResource($this);
        }

        return $this->deviceResource;
    }

    /**
     * Get the jobs resource.
     */
    public function jobs(): JobResource
    {
        if ($this->jobResource === null) {
            $this->jobResource = new JobResource($this);
        }

        return $this->jobResource;
    }

    /**
     * Make a GET request to the API.
     *
     * @throws CloudlinkerException
     */
    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Make a POST request to the API.
     *
     * @throws CloudlinkerException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Make a PUT request to the API.
     *
     * @throws CloudlinkerException
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * Make a DELETE request to the API.
     *
     * @throws CloudlinkerException
     */
    public function delete(string $endpoint, array $data = []): array
    {
        return $this->request('DELETE', $endpoint, ['json' => $data]);
    }

    /**
     * Make a request to the API.
     *
     * @throws CloudlinkerException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $endpoint, $options);
            $body = $response->getBody()->getContents();

            return json_decode($body, true) ?? [];
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleException $e) {
            throw new CloudlinkerException(
                'Failed to connect to Cloudlinker API: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Handle client exceptions and throw appropriate custom exceptions.
     *
     * @throws CloudlinkerException
     */
    protected function handleClientException(ClientException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents(), true) ?? [];

        match ($statusCode) {
            401 => throw new AuthenticationException(),
            404 => throw new NotFoundException($body['message'] ?? 'Resource not found.'),
            422 => throw new ValidationException(
                $body['message'] ?? 'Validation failed.',
                $body['errors'] ?? []
            ),
            429 => throw new RateLimitException(
                $body['message'] ?? 'Rate limit exceeded.',
                (int) ($response->getHeader('Retry-After')[0] ?? 60)
            ),
            default => throw CloudlinkerException::fromResponse($body, $statusCode),
        };
    }

    /**
     * Get the HTTP client instance.
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * Set a custom HTTP client instance.
     */
    public function setHttpClient(Client $client): self
    {
        $this->httpClient = $client;

        return $this;
    }
}
