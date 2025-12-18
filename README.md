# Cloudlinker Laravel Client

[![Tests](https://github.com/ssamoy/cloudlinker-laravel-client/actions/workflows/tests.yml/badge.svg)](https://github.com/ssamoy/cloudlinker-laravel-client/actions)
[![Latest Stable Version](https://poser.pugx.org/stesa/cloudlinker-laravel-client/v)](https://packagist.org/packages/stesa/cloudlinker-laravel-client)
[![License](https://poser.pugx.org/stesa/cloudlinker-laravel-client/license)](https://packagist.org/packages/stesa/cloudlinker-laravel-client)

Laravel package for integrating with the [Cloudlinker.eu](https://cloudlinker.eu) API. Connect printers, scanners, scales and IoT devices to your Laravel application.

## Requirements

- PHP 8.2+
- Laravel 10.x, 11.x or 12.x

## Installation

Install the package via Composer:

```bash
composer require stesa/cloudlinker-laravel-client
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=cloudlinker-config
```

Add your Cloudlinker credentials to your `.env` file:

```env
CLOUDLINKER_ORG_ID=your-organisation-id
CLOUDLINKER_API_KEY=your-api-key
```

## Usage

### Using the Facade

```php
use Stesa\CloudlinkerClient\Facades\Cloudlinker;

// Test the connection
Cloudlinker::test();

// List all clients (registered by Cloudlinker software)
$clients = Cloudlinker::clients()->list();

// Get all clients (auto-paginated)
$allClients = Cloudlinker::clients()->all();

// Filter clients by hostname
$clients = Cloudlinker::clients()->list(hostname: 'office-pc');

// Delete a client
Cloudlinker::clients()->delete('client-uuid');
```

### Working with Devices

```php
// List devices for a client
$devices = Cloudlinker::devices()->list('client-uuid');

// Get all devices for a client (auto-paginated)
$allDevices = Cloudlinker::devices()->all('client-uuid');

// Delete a device
Cloudlinker::devices()->delete('device-uuid');
```

### Working with Jobs

```php
// Create a print job
$job = Cloudlinker::jobs()->create([
    'client_id' => 'client-uuid',
    'device_id' => 'device-uuid',
    'job_type' => 1, // 1 = PRINTJOB
    'payload' => json_encode([
        'document_type' => 'pdf',
        'document_url' => 'https://example.com/document.pdf',
        'copies' => 1,
    ]),
]);

// Launch a job
$job = Cloudlinker::jobs()->launch('job-uuid');

// Create and immediately launch a job
$job = Cloudlinker::jobs()->create([
    'client_id' => 'client-uuid',
    'device_id' => 'device-uuid',
    'job_type' => 1,
    'payload' => json_encode([
        'document_type' => 'pdf',
        'document_url' => 'https://example.com/document.pdf',
        'copies' => 1,
    ]),
    'launch_immediately' => true,
]);

// Delete a job
Cloudlinker::jobs()->delete('job-uuid');
```

### Job Types

| Type | Value | Description |
|------|-------|-------------|
| PRINT | `print` | Print a PDF document to a printer |
| HTTP_COMMAND | `http_command` | Execute an HTTP request from the client machine |

### Print Jobs

```php
$job = Cloudlinker::jobs()->createAndLaunch([
    'device_id' => 'device-uuid',
    'type' => 'print',
    'source' => 'https://example.com/document.pdf',
    'options' => [
        'copies' => 1,
    ],
]);
```

### HTTP_COMMAND Jobs

HTTP_COMMAND jobs execute HTTP requests from the Cloudlinker client machine, allowing access to internal network resources that may not be accessible from the internet.

```php
// Simple GET request
$job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
    clientId: 'client-uuid',
    targetUrl: 'https://internal-api.local/status',
    method: 'GET'
);

// POST request with parameters
$job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
    clientId: 'client-uuid',
    targetUrl: 'https://internal-api.local/webhook',
    method: 'POST',
    parameters: [
        'event' => 'order_created',
        'order_id' => '12345',
    ]
);

// Request with Bearer authentication
$job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
    clientId: 'client-uuid',
    targetUrl: 'https://api.example.com/data',
    method: 'GET',
    authentication: 'bearer',
    bearerToken: 'your-api-token'
);

// Request with Basic authentication
$job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
    clientId: 'client-uuid',
    targetUrl: 'https://secure-api.local/endpoint',
    method: 'POST',
    authentication: 'basic',
    username: 'user',
    password: 'secret'
);

// Request with custom headers
$job = Cloudlinker::jobs()->createAndLaunchHttpCommand(
    clientId: 'client-uuid',
    targetUrl: 'https://api.example.com/data',
    method: 'GET',
    headers: [
        'X-Custom-Header' => 'value',
        'Accept' => 'application/json',
    ]
);

// Full control with raw create method
$job = Cloudlinker::jobs()->create([
    'client_id' => 'client-uuid',
    'job_type' => 2,  // 2 = HTTP_COMMAND
    'http_target_url' => 'https://api.example.com/webhook',
    'http_method' => 'POST',
    'http_headers' => ['Content-Type' => 'application/json'],
    'http_parameters' => ['key' => 'value'],
    'http_authentication' => 'bearer',
    'http_bearer_token' => 'your-token',
    'http_callback_method' => 'webhook',
    'http_webhook_url' => 'https://your-app.com/callback',
    'http_webhook_method' => 'POST',
]);
```

#### Reading HTTP_COMMAND Results

```php
// Check job completion
if ($job->isCompleted()) {
    // Get HTTP response details
    $statusCode = $job->getHttpStatus();      // e.g., 200
    $responseBody = $job->getHttpResult();    // Response body as string

    // Check if request was successful (2xx status)
    if ($job->isHttpSuccess()) {
        echo "Request succeeded!";
    }

    // If webhook was configured
    $webhookStatus = $job->getWebhookHttpStatus();
    $webhookResult = $job->getWebhookHttpResult();
}
```

### Using Dependency Injection

```php
use Stesa\CloudlinkerClient\CloudlinkerClient;

class PrintController extends Controller
{
    public function print(CloudlinkerClient $cloudlinker)
    {
        // Get the first available client and device
        $clients = $cloudlinker->clients()->all();
        $devices = $cloudlinker->devices()->all($clients[0]->id);

        $job = $cloudlinker->jobs()->create([
            'client_id' => $clients[0]->id,
            'device_id' => $devices[0]->id,
            'job_type' => 1,
            'payload' => json_encode([
                'document_type' => 'pdf',
                'document_url' => 'https://example.com/invoice.pdf',
                'copies' => 1,
            ]),
            'launch_immediately' => true,
        ]);

        return response()->json(['job_id' => $job->id]);
    }
}
```

## Events

The package dispatches events when API actions are performed:

| Event | Description |
|-------|-------------|
| `ClientDeleted` | Fired when a client is deleted |
| `DeviceDeleted` | Fired when a device is deleted |
| `JobCreated` | Fired when a job is created |
| `JobLaunched` | Fired when a job is launched |
| `JobDeleted` | Fired when a job is deleted |

### Listening to Events

```php
// In your EventServiceProvider
use Stesa\CloudlinkerClient\Events\JobLaunched;

protected $listen = [
    JobLaunched::class => [
        SendPrintNotification::class,
    ],
];
```

## Artisan Commands

```bash
# Test your API connection
php artisan cloudlinker:test

# List all clients
php artisan cloudlinker:clients

# Filter clients by hostname
php artisan cloudlinker:clients --hostname=office

# Test an HTTP_COMMAND job (uses first available client)
php artisan cloudlinker:http-test https://httpbin.org/get

# HTTP_COMMAND with specific client
php artisan cloudlinker:http-test https://httpbin.org/get --client=client-uuid

# HTTP_COMMAND with POST method
php artisan cloudlinker:http-test https://httpbin.org/post --method=POST

# HTTP_COMMAND with Bearer token
php artisan cloudlinker:http-test https://api.example.com/data --bearer=your-token

# HTTP_COMMAND with custom headers and parameters
php artisan cloudlinker:http-test https://api.example.com/endpoint \
    --method=POST \
    --header="Content-Type: application/json" \
    --header="X-Custom: value" \
    --param="key=value" \
    --param="another=param"
```

## DTOs

The package uses Data Transfer Objects for type-safe data handling:

```php
use Stesa\CloudlinkerClient\DTOs\Client;
use Stesa\CloudlinkerClient\DTOs\Device;
use Stesa\CloudlinkerClient\DTOs\Job;

// Client properties
$client->id;
$client->hostname;
$client->description;
$client->ipAddress;
$client->lastSeen;
$client->isOnline(); // true if last_seen is within 5 minutes

// Device properties
$device->id;
$device->clientId;
$device->name;
$device->hardwarePath;
$device->additionalInfo;

// Job properties
$job->id;
$job->clientId;
$job->deviceId;
$job->deviceName;
$job->jobType;              // Job::TYPE_PRINT (1) or Job::TYPE_HTTP_COMMAND (2)
$job->statusCode;           // Job::STATUS_CREATED (1) through STATUS_FAILED (5)
$job->payload;              // Parsed job payload as array
$job->result;               // Parsed result as array
$job->error;
$job->createdAt;

// Job status helpers
$job->isCreated();
$job->isLaunched();
$job->isPending();
$job->isProcessing();       // true if launched or pending
$job->isCompleted();
$job->isFailed();
$job->getStatusName();      // 'created', 'launched', 'pending', 'completed', 'failed'

// Job type helpers
$job->isPrintJob();
$job->isHttpCommand();
$job->getTypeName();        // 'print' or 'http_command'

// HTTP_COMMAND result helpers
$job->getHttpStatus();         // HTTP status code (e.g., 200)
$job->getHttpResult();         // Response body
$job->isHttpSuccess();         // true if status 2xx
$job->getWebhookHttpStatus();  // Webhook callback status
$job->getWebhookHttpResult();  // Webhook callback response
```

## Exception Handling

The package throws specific exceptions for different error types:

```php
use Stesa\CloudlinkerClient\Exceptions\AuthenticationException;
use Stesa\CloudlinkerClient\Exceptions\CloudlinkerException;
use Stesa\CloudlinkerClient\Exceptions\NotFoundException;
use Stesa\CloudlinkerClient\Exceptions\QuotaExceededException;
use Stesa\CloudlinkerClient\Exceptions\RateLimitException;
use Stesa\CloudlinkerClient\Exceptions\ValidationException;

try {
    Cloudlinker::jobs()->create($data);
} catch (AuthenticationException $e) {
    // Invalid credentials (401)
} catch (ValidationException $e) {
    // Validation errors (422)
    $errors = $e->getErrors();
} catch (NotFoundException $e) {
    // Resource not found (404)
} catch (QuotaExceededException $e) {
    // Quota exceeded (e.g., maximum daily jobs reached)
} catch (RateLimitException $e) {
    // Rate limit exceeded (429)
    $retryAfter = $e->getRetryAfter();
} catch (CloudlinkerException $e) {
    // Other API errors
}
```

## Configuration

```php
// config/cloudlinker.php

return [
    // Your Cloudlinker organisation ID
    'organisation_id' => env('CLOUDLINKER_ORG_ID'),

    // Your Cloudlinker API key
    'api_key' => env('CLOUDLINKER_API_KEY'),

    // API base URL (change only for staging/testing)
    'base_url' => env('CLOUDLINKER_URL', 'https://cloudlinker.eu/api'),

    // Request timeout in seconds
    'timeout' => env('CLOUDLINKER_TIMEOUT', 30),
];
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
