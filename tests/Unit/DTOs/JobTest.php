<?php

namespace Stesa\CloudlinkerClient\Tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use Stesa\CloudlinkerClient\DTOs\Job;

class JobTest extends TestCase
{
    public function test_it_can_be_created_from_array(): void
    {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440002',
            'device_id' => '550e8400-e29b-41d4-a716-446655440001',
            'type' => 'print',
            'status' => 'pending',
            'source' => 'https://example.com/document.pdf',
            'options' => ['copies' => 2],
        ];

        $job = Job::fromArray($data);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440002', $job->id);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440001', $job->deviceId);
        $this->assertEquals('print', $job->type);
        $this->assertEquals('pending', $job->status);
        $this->assertEquals('https://example.com/document.pdf', $job->source);
        $this->assertEquals(['copies' => 2], $job->options);
    }

    public function test_status_helpers_work_correctly(): void
    {
        $pending = new Job(status: 'pending');
        $this->assertTrue($pending->isPending());
        $this->assertFalse($pending->isProcessing());
        $this->assertFalse($pending->isCompleted());
        $this->assertFalse($pending->isFailed());

        $processing = new Job(status: 'processing');
        $this->assertFalse($processing->isPending());
        $this->assertTrue($processing->isProcessing());

        $completed = new Job(status: 'completed');
        $this->assertTrue($completed->isCompleted());

        $failed = new Job(status: 'failed');
        $this->assertTrue($failed->isFailed());
    }
}
