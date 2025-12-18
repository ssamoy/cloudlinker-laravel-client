<?php

/**
 * Example: Create and Launch an HTTP_COMMAND Job
 *
 * HTTP_COMMAND jobs execute HTTP requests from the Cloudlinker client machine,
 * allowing access to internal network resources that may not be accessible
 * from the internet.
 *
 * Run this in your Laravel application:
 * php artisan tinker < examples/create-http-command-job.php
 */

use Stesa\CloudlinkerClient\Facades\Cloudlinker;

echo "=== HTTP_COMMAND Job Examples ===\n\n";

try {
    // First, get a client to execute the HTTP requests from
    echo "Finding available client...\n";
    $clients = Cloudlinker::clients()->all();

    if (empty($clients)) {
        echo "No clients found. Please register a Cloudlinker client first.\n";
        exit(1);
    }

    $clientId = $clients[0]->id;
    echo "Using client: {$clients[0]->hostname} ({$clientId})\n\n";

    // Example 1: Simple GET request
    echo "1. Simple GET request\n";
    echo "   Creating job to fetch https://httpbin.org/get...\n";

    $job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
        clientId: $clientId,
        targetUrl: 'https://httpbin.org/get',
        method: 'GET'
    );

    echo "   Job created! ID: {$job->id}\n";
    echo "   Status: {$job->status}\n\n";

    // Example 2: POST request with parameters
    echo "2. POST request with parameters\n";
    echo "   Creating job to POST to https://httpbin.org/post...\n";

    $job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
        clientId: $clientId,
        targetUrl: 'https://httpbin.org/post',
        method: 'POST',
        parameters: [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]
    );

    echo "   Job created! ID: {$job->id}\n";
    echo "   Status: {$job->status}\n\n";

    // Example 3: Request with custom headers
    echo "3. Request with custom headers\n";
    echo "   Creating job with custom headers...\n";

    $job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
        clientId: $clientId,
        targetUrl: 'https://httpbin.org/headers',
        method: 'GET',
        headers: [
            'X-Custom-Header' => 'CustomValue',
            'Accept' => 'application/json',
        ]
    );

    echo "   Job created! ID: {$job->id}\n";
    echo "   Status: {$job->status}\n\n";

    // Example 4: Request with Bearer token authentication
    echo "4. Request with Bearer token authentication\n";
    echo "   Creating job with bearer auth...\n";

    $job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
        clientId: $clientId,
        targetUrl: 'https://httpbin.org/bearer',
        method: 'GET',
        authentication: 'bearer',
        bearerToken: 'your-api-token-here'
    );

    echo "   Job created! ID: {$job->id}\n";
    echo "   Status: {$job->status}\n\n";

    // Example 5: Request with basic authentication
    echo "5. Request with Basic authentication\n";
    echo "   Creating job with basic auth...\n";

    $job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
        clientId: $clientId,
        targetUrl: 'https://httpbin.org/basic-auth/user/passwd',
        method: 'GET',
        authentication: 'basic',
        username: 'user',
        password: 'passwd'
    );

    echo "   Job created! ID: {$job->id}\n";
    echo "   Status: {$job->status}\n\n";

    // Example 6: Using the raw create method for full control
    echo "6. Using raw create method with all options\n";
    echo "   Creating job with full control...\n";

    $job = Cloudlinker::jobs()->create([
        'client_id' => $clientId,
        'job_type' => 2,  // 2 = HTTP_COMMAND
        'payload' => json_encode([
            'http_target_url' => 'https://httpbin.org/anything',
            'http_method' => 'POST',
            'http_headers' => [
                'Content-Type' => 'application/json',
                'X-Request-ID' => uniqid(),
            ],
            'http_parameters' => [
                'action' => 'test',
                'timestamp' => time(),
            ],
            'http_authentication' => 'bearer',
            'http_bearer_token' => 'my-secret-token',
        ]),
    ]);

    $job = Cloudlinker::jobs()->launch($job->id);

    echo "   Job created and launched! ID: {$job->id}\n";
    echo "   Status: {$job->status}\n\n";

    echo "All examples completed!\n";
    echo "Check your Cloudlinker dashboard for job results.\n";

} catch (\Stesa\CloudlinkerClient\Exceptions\ValidationException $e) {
    echo "Validation error: " . $e->getMessage() . "\n";
    print_r($e->getErrors());
} catch (\Stesa\CloudlinkerClient\Exceptions\CloudlinkerException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
