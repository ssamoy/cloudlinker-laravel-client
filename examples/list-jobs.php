<?php

/**
 * Example: List Jobs and their Status
 *
 * Run this in your Laravel application:
 * php artisan tinker < examples/list-jobs.php
 */

use Stesa\CloudlinkerClient\Facades\Cloudlinker;

echo "=== Cloudlinker Jobs ===\n\n";

try {
    // Get all jobs
    $response = Cloudlinker::jobs()->list();
    $jobs = $response['data'];

    if (empty($jobs)) {
        echo "No jobs found.\n";
        exit;
    }

    echo "Found " . count($jobs) . " job(s):\n\n";

    foreach ($jobs as $job) {
        $statusIcon = match(true) {
            $job->isCompleted() => '[OK]',
            $job->isFailed() => '[FAILED]',
            $job->isProcessing() => '[RUNNING]',
            $job->isPending() => '[PENDING]',
            default => '[' . strtoupper($job->status) . ']',
        };

        echo "{$statusIcon} Job {$job->id}\n";
        echo "  Type: {$job->type}\n";
        echo "  Device: {$job->deviceId}\n";
        echo "  Created: {$job->createdAt}\n";

        if ($job->isFailed() && $job->error) {
            echo "  Error: {$job->error}\n";
        }

        echo "\n";
    }

    // Show pagination info
    $meta = $response['meta'];
    if (!empty($meta)) {
        echo "Page {$meta['current_page']} of {$meta['last_page']} (Total: {$meta['total']} jobs)\n";
    }

} catch (\Stesa\CloudlinkerClient\Exceptions\CloudlinkerException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
