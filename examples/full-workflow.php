<?php

/**
 * Example: Full Workflow - From Client Discovery to Print Job
 *
 * This example demonstrates a complete workflow:
 * 1. Test connection
 * 2. List available clients
 * 3. Find printer devices
 * 4. Create and launch a print job
 * 5. Check job status
 *
 * Run this in your Laravel application:
 * php artisan tinker < examples/full-workflow.php
 */

use Stesa\CloudlinkerClient\Facades\Cloudlinker;
use Stesa\CloudlinkerClient\Exceptions\CloudlinkerException;

echo "╔══════════════════════════════════════════╗\n";
echo "║   Cloudlinker Full Workflow Example      ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

try {
    // Step 1: Test connection
    echo "Step 1: Testing API connection...\n";
    if (Cloudlinker::test()) {
        echo "        ✓ Connection successful\n\n";
    }

    // Step 2: List clients
    echo "Step 2: Discovering clients...\n";
    $clients = Cloudlinker::clients()->all();

    if (empty($clients)) {
        echo "        ✗ No clients found. Install Cloudlinker software first.\n";
        exit(1);
    }

    echo "        ✓ Found " . count($clients) . " client(s)\n";
    foreach ($clients as $client) {
        $status = $client->isOnline() ? 'online' : 'offline';
        echo "          - {$client->name} ({$status})\n";
    }
    echo "\n";

    // Step 3: Find a printer
    echo "Step 3: Finding printer devices...\n";
    $printer = null;
    $printerClient = null;

    foreach ($clients as $client) {
        if (!$client->isOnline()) continue;

        $devices = Cloudlinker::devices()->all($client->id);
        foreach ($devices as $device) {
            if ($device->isPrinter()) {
                $printer = $device;
                $printerClient = $client;
                break 2;
            }
        }
    }

    if (!$printer) {
        echo "        ✗ No online printer found.\n";
        echo "          Make sure a client with a printer is online.\n";
        exit(1);
    }

    echo "        ✓ Found printer: {$printer->name}\n";
    echo "          Client: {$printerClient->name}\n";
    echo "          Device ID: {$printer->id}\n\n";

    // Step 4: Create a print job
    echo "Step 4: Creating print job...\n";
    $testDocument = 'https://cloudlinker.eu/storage/tests/test.pdf';

    $job = Cloudlinker::jobs()->create([
        'device_id' => $printer->id,
        'type' => 'print',
        'source' => $testDocument,
        'options' => [
            'copies' => 1,
        ],
    ]);

    echo "        ✓ Job created: {$job->id}\n";
    echo "          Status: {$job->status}\n\n";

    // Step 5: Launch the job
    echo "Step 5: Launching job...\n";
    $job = Cloudlinker::jobs()->launch($job->id);

    echo "        ✓ Job launched!\n";
    echo "          Status: {$job->status}\n\n";

    // Summary
    echo "╔══════════════════════════════════════════╗\n";
    echo "║   Workflow Complete!                     ║\n";
    echo "╚══════════════════════════════════════════╝\n";
    echo "\n";
    echo "Job ID: {$job->id}\n";
    echo "The document should now be printing on: {$printer->name}\n";

} catch (CloudlinkerException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
