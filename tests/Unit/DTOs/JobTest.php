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
            'client_id' => '550e8400-e29b-41d4-a716-446655440000',
            'device_id' => '550e8400-e29b-41d4-a716-446655440001',
            'device_name' => 'Test Printer',
            'job_type' => 1,
            'status' => 1,
            'payload' => '{"document_url":"https://example.com/doc.pdf","copies":2}',
            'created_at' => '2025-12-18T12:00:00.000000Z',
        ];

        $job = Job::fromArray($data);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440002', $job->id);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $job->clientId);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440001', $job->deviceId);
        $this->assertEquals('Test Printer', $job->deviceName);
        $this->assertEquals(Job::TYPE_PRINT, $job->jobType);
        $this->assertEquals(Job::STATUS_CREATED, $job->statusCode);
        $this->assertEquals(['document_url' => 'https://example.com/doc.pdf', 'copies' => 2], $job->payload);
    }

    public function test_status_helpers_work_correctly(): void
    {
        $created = new Job(statusCode: Job::STATUS_CREATED);
        $this->assertTrue($created->isCreated());
        $this->assertFalse($created->isLaunched());
        $this->assertFalse($created->isPending());
        $this->assertFalse($created->isCompleted());
        $this->assertFalse($created->isFailed());

        $launched = new Job(statusCode: Job::STATUS_LAUNCHED);
        $this->assertTrue($launched->isLaunched());
        $this->assertTrue($launched->isProcessing());

        $pending = new Job(statusCode: Job::STATUS_PENDING);
        $this->assertTrue($pending->isPending());
        $this->assertTrue($pending->isProcessing());

        $completed = new Job(statusCode: Job::STATUS_COMPLETED);
        $this->assertTrue($completed->isCompleted());
        $this->assertFalse($completed->isProcessing());

        $failed = new Job(statusCode: Job::STATUS_FAILED);
        $this->assertTrue($failed->isFailed());
    }

    public function test_type_helpers_work_correctly(): void
    {
        $printJob = new Job(jobType: Job::TYPE_PRINT);
        $this->assertTrue($printJob->isPrintJob());
        $this->assertFalse($printJob->isHttpCommand());
        $this->assertEquals('print', $printJob->getTypeName());

        $httpJob = new Job(jobType: Job::TYPE_HTTP_COMMAND);
        $this->assertFalse($httpJob->isPrintJob());
        $this->assertTrue($httpJob->isHttpCommand());
        $this->assertEquals('http_command', $httpJob->getTypeName());
    }

    public function test_status_name_helpers(): void
    {
        $this->assertEquals('created', (new Job(statusCode: Job::STATUS_CREATED))->getStatusName());
        $this->assertEquals('launched', (new Job(statusCode: Job::STATUS_LAUNCHED))->getStatusName());
        $this->assertEquals('pending', (new Job(statusCode: Job::STATUS_PENDING))->getStatusName());
        $this->assertEquals('completed', (new Job(statusCode: Job::STATUS_COMPLETED))->getStatusName());
        $this->assertEquals('failed', (new Job(statusCode: Job::STATUS_FAILED))->getStatusName());
        $this->assertEquals('unknown', (new Job(statusCode: 99))->getStatusName());
    }

    public function test_http_result_helpers_return_correct_values(): void
    {
        $job = new Job(
            jobType: Job::TYPE_HTTP_COMMAND,
            statusCode: Job::STATUS_COMPLETED,
            result: [
                'http_status' => 200,
                'http_result' => '{"success": true}',
                'webhook_http_status' => 201,
                'webhook_http_result' => 'OK',
            ]
        );

        $this->assertEquals(200, $job->getHttpStatus());
        $this->assertEquals('{"success": true}', $job->getHttpResult());
        $this->assertEquals(201, $job->getWebhookHttpStatus());
        $this->assertEquals('OK', $job->getWebhookHttpResult());
    }

    public function test_http_result_helpers_return_null_when_missing(): void
    {
        $job = new Job(jobType: Job::TYPE_HTTP_COMMAND, statusCode: Job::STATUS_PENDING);

        $this->assertNull($job->getHttpStatus());
        $this->assertNull($job->getHttpResult());
        $this->assertNull($job->getWebhookHttpStatus());
        $this->assertNull($job->getWebhookHttpResult());
    }

    public function test_http_result_helpers_return_null_with_empty_result(): void
    {
        $job = new Job(jobType: Job::TYPE_HTTP_COMMAND, statusCode: Job::STATUS_COMPLETED, result: []);

        $this->assertNull($job->getHttpStatus());
        $this->assertNull($job->getHttpResult());
    }

    public function test_is_http_success_returns_true_for_2xx_status(): void
    {
        $job200 = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: ['http_status' => 200]);
        $this->assertTrue($job200->isHttpSuccess());

        $job201 = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: ['http_status' => 201]);
        $this->assertTrue($job201->isHttpSuccess());

        $job299 = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: ['http_status' => 299]);
        $this->assertTrue($job299->isHttpSuccess());
    }

    public function test_is_http_success_returns_false_for_non_2xx_status(): void
    {
        $job400 = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: ['http_status' => 400]);
        $this->assertFalse($job400->isHttpSuccess());

        $job404 = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: ['http_status' => 404]);
        $this->assertFalse($job404->isHttpSuccess());

        $job500 = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: ['http_status' => 500]);
        $this->assertFalse($job500->isHttpSuccess());

        $job199 = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: ['http_status' => 199]);
        $this->assertFalse($job199->isHttpSuccess());
    }

    public function test_is_http_success_returns_false_when_no_status(): void
    {
        $jobNoResult = new Job(jobType: Job::TYPE_HTTP_COMMAND);
        $this->assertFalse($jobNoResult->isHttpSuccess());

        $jobEmptyResult = new Job(jobType: Job::TYPE_HTTP_COMMAND, result: []);
        $this->assertFalse($jobEmptyResult->isHttpSuccess());
    }

    public function test_payload_is_parsed_from_json_string(): void
    {
        $data = [
            'id' => 'test-id',
            'job_type' => 2,
            'status' => 1,
            'payload' => '{"http_target_url":"https://example.com","http_method":"GET"}',
        ];

        $job = Job::fromArray($data);

        $this->assertIsArray($job->payload);
        $this->assertEquals('https://example.com', $job->payload['http_target_url']);
        $this->assertEquals('GET', $job->payload['http_method']);
    }

    public function test_result_is_parsed_from_json_string(): void
    {
        $data = [
            'id' => 'test-id',
            'job_type' => 2,
            'status' => 4,
            'result' => '{"http_status":200,"http_result":"OK"}',
        ];

        $job = Job::fromArray($data);

        $this->assertIsArray($job->result);
        $this->assertEquals(200, $job->getHttpStatus());
        $this->assertEquals('OK', $job->getHttpResult());
    }
}
