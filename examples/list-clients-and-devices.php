<?php

/**
 * Example: List all Clients and their Devices
 *
 * Run this in your Laravel application:
 * php artisan tinker < examples/list-clients-and-devices.php
 */

use Stesa\CloudlinkerClient\Facades\Cloudlinker;

echo "=== Cloudlinker Clients & Devices ===\n\n";

try {
    // Get all clients
    $clients = Cloudlinker::clients()->all();

    if (empty($clients)) {
        echo "No clients found. Make sure Cloudlinker software is installed and registered.\n";
        exit;
    }

    echo "Found " . count($clients) . " client(s):\n\n";

    foreach ($clients as $client) {
        $status = $client->isOnline() ? '[ONLINE]' : '[OFFLINE]';
        echo "Client: {$client->name} {$status}\n";
        echo "  ID: {$client->id}\n";
        echo "  Hostname: {$client->hostname}\n";
        echo "  Last seen: {$client->lastSeen}\n";

        // Get devices for this client
        $devices = Cloudlinker::devices()->all($client->id);

        if (empty($devices)) {
            echo "  Devices: None\n";
        } else {
            echo "  Devices:\n";
            foreach ($devices as $device) {
                $type = strtoupper($device->type);
                echo "    - [{$type}] {$device->name} (ID: {$device->id})\n";
            }
        }

        echo "\n";
    }

} catch (\Stesa\CloudlinkerClient\Exceptions\CloudlinkerException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
