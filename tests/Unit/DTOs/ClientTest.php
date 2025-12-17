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
            'hostname' => 'test-host',
            'description' => 'Test Client Description',
            'ip_address' => '192.168.1.100',
            'last_seen' => '2024-01-15 10:00:00',
        ];

        $client = Client::fromArray($data);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $client->id);
        $this->assertEquals('test-host', $client->hostname);
        $this->assertEquals('Test Client Description', $client->description);
        $this->assertEquals('192.168.1.100', $client->ipAddress);
        $this->assertEquals('2024-01-15 10:00:00', $client->lastSeen);
    }

    public function test_it_can_be_converted_to_array(): void
    {
        $client = new Client(
            id: '550e8400-e29b-41d4-a716-446655440000',
            hostname: 'test-host',
            description: 'Test Description',
            ipAddress: '192.168.1.100',
        );

        $array = $client->toArray();

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $array['id']);
        $this->assertEquals('test-host', $array['hostname']);
        $this->assertEquals('Test Description', $array['description']);
        $this->assertEquals('192.168.1.100', $array['ip_address']);
    }

    public function test_is_online_returns_true_when_last_seen_is_recent(): void
    {
        $recentTime = (new \DateTime())->modify('-2 minutes')->format('Y-m-d H:i:s');
        $client = new Client(lastSeen: $recentTime);

        $this->assertTrue($client->isOnline());
    }

    public function test_is_online_returns_false_when_last_seen_is_old(): void
    {
        $oldTime = (new \DateTime())->modify('-10 minutes')->format('Y-m-d H:i:s');
        $client = new Client(lastSeen: $oldTime);

        $this->assertFalse($client->isOnline());
    }

    public function test_is_online_returns_false_when_last_seen_is_null(): void
    {
        $client = new Client(lastSeen: null);

        $this->assertFalse($client->isOnline());
    }
}
