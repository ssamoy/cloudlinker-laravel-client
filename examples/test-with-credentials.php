<?php

/**
 * Test script with specific credentials
 *
 * Run this in your Laravel application:
 * php artisan tinker < examples/test-with-credentials.php
 *
 * Or configure these in your .env file:
 * CLOUDLINKER_ORG_ID=0197f098-408f-7082-8519-3d340d852ef3
 * CLOUDLINKER_API_KEY=your-api-key
 */

use Stesa\CloudlinkerClient\Facades\Cloudlinker;

// Your test credentials
$clientId = '019b2336-f721-70d9-bb72-ed0c97790328';
$organizationId = '0197f098-408f-7082-8519-3d340d852ef3';

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║              Cloudlinker API Test                            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "Organization ID: {$organizationId}\n";
echo "Test Client ID: {$clientId}\n\n";

try {
    // 1. Test connection
    echo "1. Testing API connection...\n";
    $connected = Cloudlinker::test();
    echo "   ✓ Connection: " . ($connected ? 'OK' : 'FAILED') . "\n\n";

    // 2. List all clients
    echo "2. Listing clients...\n";
    $clients = Cloudlinker::clients()->all();
    echo "   ✓ Found " . count($clients) . " client(s)\n";
    foreach ($clients as $client) {
        $status = $client->isOnline() ? 'ONLINE' : 'OFFLINE';
        echo "   - [{$status}] {$client->name} ({$client->id})\n";
    }
    echo "\n";

    // 3. Get devices for specific client
    echo "3. Getting devices for client {$clientId}...\n";
    $devices = Cloudlinker::devices()->all($clientId);
    echo "   ✓ Found " . count($devices) . " device(s)\n";
    foreach ($devices as $device) {
        $type = strtoupper($device->type);
        echo "   - [{$type}] {$device->name} ({$device->id})\n";
    }
    echo "\n";

    // 4. List recent jobs
    echo "4. Listing recent jobs...\n";
    $response = Cloudlinker::jobs()->list(perPage: 10);
    $jobs = $response['data'];
    echo "   ✓ Found " . count($jobs) . " job(s)\n";
    foreach ($jobs as $job) {
        $status = strtoupper($job->status);
        echo "   - [{$status}] {$job->type} job ({$job->id})\n";
    }
    echo "\n";

    echo "╔══════════════════════════════════════════════════════════════╗\n";
    echo "║              All tests completed successfully!               ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n";

} catch (\Stesa\CloudlinkerClient\Exceptions\AuthenticationException $e) {
    echo "\n✗ Authentication failed!\n";
    echo "  Check your CLOUDLINKER_ORG_ID and CLOUDLINKER_API_KEY in .env\n";
} catch (\Stesa\CloudlinkerClient\Exceptions\CloudlinkerException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
}
