<?php

namespace Stesa\CloudlinkerClient\Commands;

use Illuminate\Console\Command;
use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\Exceptions\CloudlinkerException;

class HttpCommandTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudlinker:http-test
                            {url : The target URL to call}
                            {--client= : Client ID to execute from (uses first available if not specified)}
                            {--method=GET : HTTP method (GET or POST)}
                            {--bearer= : Bearer token for authentication}
                            {--header=* : Custom headers in format "Name: Value"}
                            {--param=* : Request parameters in format "name=value"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test an HTTP_COMMAND job by making an HTTP request from a Cloudlinker client';

    /**
     * Execute the console command.
     */
    public function handle(CloudlinkerClient $client): int
    {
        $url = $this->argument('url');
        $clientId = $this->option('client');
        $method = strtoupper($this->option('method'));
        $bearerToken = $this->option('bearer');
        $headerOptions = $this->option('header');
        $paramOptions = $this->option('param');

        // Validate method
        if (!in_array($method, ['GET', 'POST'])) {
            $this->error('Invalid HTTP method. Use GET or POST.');

            return self::FAILURE;
        }

        // Parse headers
        $headers = $this->parseKeyValueOptions($headerOptions, ':');

        // Parse parameters
        $parameters = $this->parseKeyValueOptions($paramOptions, '=');

        try {
            // Get client ID if not provided
            if (!$clientId) {
                $this->info("Finding available client...");
                $clients = $client->clients()->all();

                if (empty($clients)) {
                    $this->error('No Cloudlinker clients found. Please ensure a client is registered.');

                    return self::FAILURE;
                }

                $clientId = $clients[0]->id;
                $this->line("  Using client: {$clients[0]->hostname} ({$clientId})");
                $this->newLine();
            }

            $this->info("Creating HTTP_COMMAND job...");
            $this->line("  Target URL: {$url}");
            $this->line("  Method: {$method}");

            if ($bearerToken) {
                $this->line("  Authentication: Bearer token");
            }

            if (!empty($headers)) {
                $this->line("  Headers: " . count($headers) . " custom header(s)");
            }

            if (!empty($parameters)) {
                $this->line("  Parameters: " . count($parameters) . " parameter(s)");
            }

            $this->newLine();

            // Create and launch the job
            $job = $client->jobs()->createAndLaunchHttpCommand(
                clientId: $clientId,
                targetUrl: $url,
                method: $method,
                headers: !empty($headers) ? $headers : null,
                parameters: !empty($parameters) ? $parameters : null,
                authentication: $bearerToken ? 'bearer' : null,
                bearerToken: $bearerToken
            );

            $this->info("Job created and launched!");
            $this->line("  Job ID: {$job->id}");
            $this->line("  Status: {$job->status}");
            $this->newLine();

            $this->line("The job has been sent to the Cloudlinker client for execution.");
            $this->line("Check the job status in your Cloudlinker dashboard or via the API.");

            return self::SUCCESS;
        } catch (CloudlinkerException $e) {
            $this->error('Failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Parse key-value options from command line.
     */
    private function parseKeyValueOptions(array $options, string $separator): array
    {
        $result = [];

        foreach ($options as $option) {
            $parts = explode($separator, $option, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
