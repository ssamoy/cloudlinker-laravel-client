<?php

namespace Stesa\CloudlinkerClient\Tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use Stesa\CloudlinkerClient\DTOs\Device;

class DeviceTest extends TestCase
{
    public function test_it_can_be_created_from_array(): void
    {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'client_id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Office Printer',
            'hardware_path' => 'USB001',
            'additional_info' => ['driver' => 'escpos'],
        ];

        $device = Device::fromArray($data);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440001', $device->id);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $device->clientId);
        $this->assertEquals('Office Printer', $device->name);
        $this->assertEquals('USB001', $device->hardwarePath);
        $this->assertEquals(['driver' => 'escpos'], $device->additionalInfo);
    }

    public function test_it_can_be_converted_to_array(): void
    {
        $device = new Device(
            id: '550e8400-e29b-41d4-a716-446655440001',
            clientId: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Office Printer',
            hardwarePath: 'USB001',
        );

        $array = $device->toArray();

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440001', $array['id']);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $array['client_id']);
        $this->assertEquals('Office Printer', $array['name']);
        $this->assertEquals('USB001', $array['hardware_path']);
    }

    public function test_it_handles_null_values(): void
    {
        $device = new Device(
            id: '550e8400-e29b-41d4-a716-446655440001',
        );

        $array = $device->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayNotHasKey('client_id', $array);
        $this->assertArrayNotHasKey('name', $array);
    }
}
