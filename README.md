# Cloudlinker Laravel Client

[![Tests](https://github.com/ssamoy/cloudlinker-laravel-client/actions/workflows/tests.yml/badge.svg)](https://github.com/ssamoy/cloudlinker-laravel-client/actions)
[![Latest Stable Version](https://poser.pugx.org/stesa/cloudlinker-laravel-client/v)](https://packagist.org/packages/stesa/cloudlinker-laravel-client)
[![License](https://poser.pugx.org/stesa/cloudlinker-laravel-client/license)](https://packagist.org/packages/stesa/cloudlinker-laravel-client)

Laravel package for integrating with the [Cloudlinker.eu](https://cloudlinker.eu) API. Connect printers, scanners, scales and IoT devices to your Laravel application.

## Requirements

- PHP 8.1+
- Laravel 10.x or 11.x

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
// List all jobs
$jobs = Cloudlinker::jobs()->list();

// List jobs for a specific device
$jobs = Cloudlinker::jobs()->list(deviceId: 'device-uuid');

// Filter by status
$pendingJobs = Cloudlinker::jobs()->list(status: 'pending');

// Create a print job
$job = Cloudlinker::jobs()->create([
    'device_id' => 'device-uuid',
    'type' => 'print',
    'source' => 'https://example.com/document.pdf',
    'options' => [
        'copies' => 2,
    ],
]);

// Launch a job
$job = Cloudlinker::jobs()->launch('job-uuid');

// Create and immediately launch a job
$job = Cloudlinker::jobs()->createAndLaunch([
    'device_id' => 'device-uuid',
    'type' => 'print',
    'source' => 'https://example.com/document.pdf',
]);

// Delete a job
Cloudlinker::jobs()->delete('job-uuid');
```

### Using Dependency Injection

```php
use Stesa\CloudlinkerClient\CloudlinkerClient;

class PrintController extends Controller
{
    public function print(CloudlinkerClient $cloudlinker)
    {
        $job = $cloudlinker->jobs()->createAndLaunch([
            'device_id' => 'device-uuid',
            'type' => 'print',
            'source' => 'https://example.com/invoice.pdf',
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
```

## DTOs

The package uses Data Transfer Objects for type-safe data handling:

```php
use Stesa\CloudlinkerClient\DTOs\Client;
use Stesa\CloudlinkerClient\DTOs\Device;
use Stesa\CloudlinkerClient\DTOs\Job;

// DTOs have helpful methods
$client->isOnline();

$device->isPrinter();
$device->isScanner();
$device->isScale();

$job->isPending();
$job->isProcessing();
$job->isCompleted();
$job->isFailed();
```

## Exception Handling

The package throws specific exceptions for different error types:

```php
use Stesa\CloudlinkerClient\Exceptions\AuthenticationException;
use Stesa\CloudlinkerClient\Exceptions\CloudlinkerException;
use Stesa\CloudlinkerClient\Exceptions\NotFoundException;
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
