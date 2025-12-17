<?php

namespace Stesa\CloudlinkerClient\Tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use Stesa\CloudlinkerClient\DTOs\Client;

class ClientTest extends TestCase
{
    public function test_it_can_be_created_from_array(): void
    {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Test Client',
            'hostname' => 'test-host',
            'status' => 'online',
            'last_seen' => '2024-01-15 10:00:00',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-15 10:00:00',
        ];

        $client = Client::fromArray($data);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $client->id);
        $this->assertEquals('Test Client', $client->name);
        $this->assertEquals('test-host', $client->hostname);
        $this->assertEquals('online', $client->status);
        $this->assertEquals('2024-01-15 10:00:00', $client->lastSeen);
    }

    public function test_it_can_be_converted_to_array(): void
    {
        $client = new Client(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Test Client',
            hostname: 'test-host',
            status: 'online',
        );

        $array = $client->toArray();

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $array['id']);
        $this->assertEquals('Test Client', $array['name']);
        $this->assertEquals('test-host', $array['hostname']);
        $this->assertEquals('online', $array['status']);
    }

    public function test_is_online_returns_true_when_status_is_online(): void
    {
        $client = new Client(status: 'online');

        $this->assertTrue($client->isOnline());
    }

    public function test_is_online_returns_false_when_status_is_offline(): void
    {
        $client = new Client(status: 'offline');

        $this->assertFalse($client->isOnline());
    }
}
