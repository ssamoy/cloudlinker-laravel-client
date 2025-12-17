<?php

/**
 * Example: Create and Launch a Print Job
 *
 * Run this in your Laravel application:
 * php artisan tinker < examples/create-print-job.php
 *
 * Make sure to replace DEVICE_UUID with an actual device ID from your account.
 */

use Stesa\CloudlinkerClient\Facades\Cloudlinker;

// Replace with your actual device UUID
$deviceId = 'YOUR_DEVICE_UUID_HERE';
$documentUrl = 'https://cloudlinker.eu/storage/tests/test.pdf';

echo "=== Create Print Job ===\n\n";

try {
    // First, let's find a printer device if we don't have a device ID
    if ($deviceId === 'YOUR_DEVICE_UUID_HERE') {
        echo "Searching for available printer devices...\n";

        $clients = Cloudlinker::clients()->all();
        $printer = null;

        foreach ($clients as $client) {
            $devices = Cloudlinker::devices()->all($client->id);
            foreach ($devices as $device) {
                if ($device->isPrinter()) {
                    $printer = $device;
                    echo "Found printer: {$device->name} (ID: {$device->id})\n";
                    break 2;
                }
            }
        }

        if (!$printer) {
            echo "No printer devices found. Please register a printer in Cloudlinker first.\n";
            exit;
        }

        $deviceId = $printer->id;
    }

    echo "\nCreating print job for device: {$deviceId}\n";
    echo "Document: {$documentUrl}\n\n";

    // Create and launch the job in one call
    $job = Cloudlinker::jobs()->createAndLaunch([
        'device_id' => $deviceId,
        'type' => 'print',
        'source' => $documentUrl,
        'options' => [
            'copies' => 1,
        ],
    ]);

    echo "Job created and launched successfully!\n";
    echo "  Job ID: {$job->id}\n";
    echo "  Status: {$job->status}\n";
    echo "  Type: {$job->type}\n";

} catch (\Stesa\CloudlinkerClient\Exceptions\ValidationException $e) {
    echo "Validation error: " . $e->getMessage() . "\n";
    print_r($e->getErrors());
} catch (\Stesa\CloudlinkerClient\Exceptions\CloudlinkerException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
