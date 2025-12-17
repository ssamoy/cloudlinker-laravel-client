<?php

/**
 * Example Routes for Cloudlinker Testing
 *
 * Add these routes to your Laravel application's routes/web.php file:
 */

use Illuminate\Support\Facades\Route;
use Stesa\CloudlinkerClient\Facades\Cloudlinker;

Route::get('/cloudlinker/test', function () {
    $data = [
        'connected' => false,
        'clients' => [],
        'devices' => [],
        'jobs' => [],
        'error' => null,
    ];

    try {
        // Test connection
        $data['connected'] = Cloudlinker::test();

        // Get all clients
        $data['clients'] = Cloudlinker::clients()->all();

        // Get devices for each client
        foreach ($data['clients'] as $client) {
            $devices = Cloudlinker::devices()->all($client->id);
            $data['devices'] = array_merge($data['devices'], $devices);
        }

        // Get all jobs
        $response = Cloudlinker::jobs()->list(perPage: 50);
        $data['jobs'] = $response['data'];

    } catch (\Exception $e) {
        $data['error'] = $e->getMessage();
    }

    return view('cloudlinker-test', $data);
});
