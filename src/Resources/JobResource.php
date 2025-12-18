<?php

namespace Stesa\CloudlinkerClient\Resources;

use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\Concerns\DispatchesEvents;
use Stesa\CloudlinkerClient\DTOs\Job;
use Stesa\CloudlinkerClient\Events\JobCreated;
use Stesa\CloudlinkerClient\Events\JobDeleted;
use Stesa\CloudlinkerClient\Events\JobLaunched;

class JobResource
{
    use DispatchesEvents;

    public function __construct(
        protected CloudlinkerClient $client
    ) {
    }

    /**
     * List jobs.
     *
     * @return array{data: Job[], meta: array, links: array}
     */
    public function list(?string $deviceId = null, ?string $status = null, int $page = 1, int $perPage = 15): array
    {
        $data = array_filter([
            'device_id' => $deviceId,
            'status' => $status,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        $response = $this->client->post('jobs/list', $data);

        return [
            'data' => array_map(
                fn (array $item) => Job::fromArray($item),
                $response['data'] ?? []
            ),
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ];
    }

    /**
     * Get all jobs (auto-paginated).
     *
     * @return Job[]
     */
    public function all(?string $deviceId = null, ?string $status = null): array
    {
        $jobs = [];
        $page = 1;

        do {
            $response = $this->list($deviceId, $status, $page);
            $jobs = array_merge($jobs, $response['data']);
            $page++;
        } while (isset($response['meta']['last_page']) && $page <= $response['meta']['last_page']);

        return $jobs;
    }

    /**
     * Create a new job.
     */
    public function create(array $data): Job
    {
        $response = $this->client->post('jobs/create', $data);

        $job = Job::fromArray($response['data'] ?? $response);

        $this->dispatchEvent(new JobCreated($job));

        return $job;
    }

    /**
     * Get a specific job by ID.
     */
    public function get(string $id): Job
    {
        $response = $this->client->post('jobs/get', ['job_id' => $id]);

        return Job::fromArray($response['data'] ?? $response);
    }

    /**
     * Launch a job.
     */
    public function launch(string $id): Job
    {
        $this->client->post('jobs/launch', ['job_id' => $id]);

        // Fetch the updated job since launch returns empty response
        $job = $this->get($id);

        $this->dispatchEvent(new JobLaunched($job));

        return $job;
    }

    /**
     * Delete a job.
     */
    public function delete(string $id): bool
    {
        $response = $this->client->post('jobs/delete', ['job_id' => $id]);

        $success = ($response['success'] ?? false) === true;

        if ($success) {
            $this->dispatchEvent(new JobDeleted($id));
        }

        return $success;
    }

    /**
     * Create and immediately launch a job.
     */
    public function createAndLaunch(array $data): Job
    {
        $job = $this->create($data);

        return $this->launch($job->id);
    }

    /**
     * Create an HTTP_COMMAND job.
     *
     * HTTP_COMMAND jobs execute HTTP requests from the client machine,
     * allowing access to internal network resources.
     *
     * @param string $clientId The Cloudlinker client ID to execute the request from
     * @param string $targetUrl The URL to call
     * @param string $method HTTP method (GET or POST)
     * @param array|null $headers Custom headers as associative array
     * @param array|null $parameters Request parameters as associative array
     * @param string|null $authentication Authentication type: 'none', 'basic', or 'bearer'
     * @param string|null $username Username for basic authentication
     * @param string|null $password Password for basic authentication
     * @param string|null $bearerToken Token for bearer authentication
     * @param string|null $webhookUrl Callback URL after job completion
     * @param string|null $webhookMethod Callback HTTP method (GET or POST)
     */
    public function createHttpCommand(
        string $clientId,
        string $targetUrl,
        string $method = 'GET',
        ?array $headers = null,
        ?array $parameters = null,
        ?string $authentication = null,
        ?string $username = null,
        ?string $password = null,
        ?string $bearerToken = null,
        ?string $webhookUrl = null,
        ?string $webhookMethod = null
    ): Job {
        $data = [
            'client_id' => $clientId,
            'job_type' => 2,  // 2 = HTTP_COMMAND
            'http_target_url' => $targetUrl,
            'http_method' => strtoupper($method),
        ];

        if ($headers !== null) {
            $data['http_headers'] = $headers;
        }

        if ($parameters !== null) {
            $data['http_parameters'] = $parameters;
        }

        if ($authentication !== null) {
            $data['http_authentication'] = $authentication;

            if ($authentication === 'basic') {
                if ($username !== null) {
                    $data['http_username'] = $username;
                }
                if ($password !== null) {
                    $data['http_password'] = $password;
                }
            } elseif ($authentication === 'bearer' && $bearerToken !== null) {
                $data['http_bearer_token'] = $bearerToken;
            }
        }

        if ($webhookUrl !== null) {
            $data['http_callback_method'] = 'webhook';
            $data['http_webhook_url'] = $webhookUrl;
            if ($webhookMethod !== null) {
                $data['http_webhook_method'] = strtoupper($webhookMethod);
            }
        }

        return $this->create($data);
    }

    /**
     * Create and immediately launch an HTTP_COMMAND job.
     */
    public function createAndLaunchHttpCommand(
        string $clientId,
        string $targetUrl,
        string $method = 'GET',
        ?array $headers = null,
        ?array $parameters = null,
        ?string $authentication = null,
        ?string $username = null,
        ?string $password = null,
        ?string $bearerToken = null,
        ?string $webhookUrl = null,
        ?string $webhookMethod = null
    ): Job {
        $job = $this->createHttpCommand(
            $clientId,
            $targetUrl,
            $method,
            $headers,
            $parameters,
            $authentication,
            $username,
            $password,
            $bearerToken,
            $webhookUrl,
            $webhookMethod
        );

        return $this->launch($job->id);
    }
}
