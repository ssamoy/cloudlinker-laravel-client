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
            'type' => 'printer',
            'driver' => 'escpos',
            'connection' => 'usb',
            'settings' => ['paper_size' => 'A4'],
            'status' => 'ready',
        ];

        $device = Device::fromArray($data);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440001', $device->id);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $device->clientId);
        $this->assertEquals('Office Printer', $device->name);
        $this->assertEquals('printer', $device->type);
        $this->assertEquals('escpos', $device->driver);
        $this->assertEquals(['paper_size' => 'A4'], $device->settings);
    }

    public function test_is_printer_returns_true_for_printer_type(): void
    {
        $device = new Device(type: 'printer');

        $this->assertTrue($device->isPrinter());
        $this->assertFalse($device->isScanner());
        $this->assertFalse($device->isScale());
    }

    public function test_is_scanner_returns_true_for_scanner_type(): void
    {
        $device = new Device(type: 'scanner');

        $this->assertFalse($device->isPrinter());
        $this->assertTrue($device->isScanner());
        $this->assertFalse($device->isScale());
    }

    public function test_is_scale_returns_true_for_scale_type(): void
    {
        $device = new Device(type: 'scale');

        $this->assertFalse($device->isPrinter());
        $this->assertFalse($device->isScanner());
        $this->assertTrue($device->isScale());
    }
}
