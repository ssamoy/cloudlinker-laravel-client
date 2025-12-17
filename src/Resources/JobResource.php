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
     * Launch a job.
     */
    public function launch(string $id): Job
    {
        $response = $this->client->post('jobs/launch', ['job_id' => $id]);

        $job = Job::fromArray($response['data'] ?? $response);

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
}
