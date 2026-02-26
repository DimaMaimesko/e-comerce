<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Shipping\Carriers\ShippingCarrierFactory;
use App\Services\Shipping\Carriers\ValueObjects\Address;

class ShippingAdapterTest extends TestCase
{
    private ShippingCarrierFactory $factory;
    private Address $from;
    private Address $to;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ShippingCarrierFactory();
        $this->from = new Address('123 Main St', 'Los Angeles', 'CA', '90001');
        $this->to = new Address('456 Oak Ave', 'New York', 'NY', '10001');
    }

    public function test_fedex_adapter_calculates_rate(): void
    {
        $carrier = $this->factory->create('FEDEX');

        $rate = $carrier->calculateRate($this->from, $this->to, 5.0);

        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }

    public function test_fedex_adapter_creates_shipment(): void
    {
        $carrier = $this->factory->create('FEDEX');

        $label = $carrier->createShipment($this->from, $this->to, 5.0, []);

        $this->assertStringStartsWith('FEDEX_', $label->trackingNumber);
        $this->assertEquals('FedEx', $label->carrier);
        $this->assertGreaterThan(0, $label->cost);
        $this->assertIsString($label->labelUrl);
    }

    public function test_ups_adapter_creates_shipment(): void
    {
        $carrier = $this->factory->create('UPS');

        $label = $carrier->createShipment($this->from, $this->to, 5.0, []);

        $this->assertStringStartsWith('UPS_', $label->trackingNumber);
        $this->assertEquals('UPS', $label->carrier);
    }

    public function test_dhl_adapter_creates_shipment(): void
    {
        $carrier = $this->factory->create('DHL');

        $label = $carrier->createShipment($this->from, $this->to, 5.0, []);

        $this->assertStringStartsWith('DHL_', $label->trackingNumber);
        $this->assertEquals('DHL', $label->carrier);
    }


    public function test_factory_throws_exception_for_unknown_carrier(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->create('UNKNOWN_CARRIER');
    }

    public function test_factory_returns_all_supported_carriers(): void
    {
        $carriers = $this->factory->getAllCarriers();

        $this->assertCount(3, $carriers);
        $this->assertArrayHasKey('FedEx', $carriers);
        $this->assertArrayHasKey('UPS', $carriers);
        $this->assertArrayHasKey('DHL', $carriers);
    }
}
