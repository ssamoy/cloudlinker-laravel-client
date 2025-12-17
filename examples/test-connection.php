<?php

/**
 * Example: Test Cloudlinker API Connection
 *
 * Run this in your Laravel application after installing the package:
 * php artisan tinker < examples/test-connection.php
 *
 * Or use the artisan command:
 * php artisan cloudlinker:test
 */

use Stesa\CloudlinkerClient\Facades\Cloudlinker;

echo "Testing Cloudlinker API connection...\n";

try {
    $result = Cloudlinker::test();

    if ($result) {
        echo "Connection successful! Your credentials are valid.\n";
    } else {
        echo "Connection test returned unexpected result.\n";
    }
} catch (\Stesa\CloudlinkerClient\Exceptions\AuthenticationException $e) {
    echo "Authentication failed! Check your CLOUDLINKER_ORG_ID and CLOUDLINKER_API_KEY.\n";
} catch (\Stesa\CloudlinkerClient\Exceptions\CloudlinkerException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
