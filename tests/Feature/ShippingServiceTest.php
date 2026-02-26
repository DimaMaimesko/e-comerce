<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Services\Shipping\Carriers\ShippingService;
use App\Services\Shipping\Carriers\ShippingCarrierFactory;

class ShippingServiceTest extends TestCase
{
    private ShippingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ShippingService(new ShippingCarrierFactory());
    }

    public function test_creates_shipment_with_fedex(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['weight' => 2.5]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'weight' => $product->weight,
                    'price' => $product->price,
                ],
            ],
        ]);

        $label = $this->service->createShipment($order, 'FEDEX');

        $this->assertStringStartsWith('FEDEX_', $label->trackingNumber);
        $this->assertEquals('FedEx', $label->carrier);
        $this->assertGreaterThan(0, $label->cost);
    }

    public function test_compares_rates_across_carriers(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['weight' => 5.0]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'weight' => 5.0, 'price' => 100],
            ],
        ]);

        $rates = $this->service->compareRates($order);

        $this->assertCount(3, $rates);

        foreach ($rates as $rate) {
            $this->assertArrayHasKey('carrier', $rate);
            if (!isset($rate['error'])) {
                $this->assertArrayHasKey('rate', $rate);
                $this->assertGreaterThan(0, $rate['rate']);
            }
        }
    }

    public function test_tracks_shipment(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1, 'weight' => 2.0, 'price' => 50]],
        ]);

        $label = $this->service->createShipment($order, 'UPS');
        $tracking = $this->service->trackShipment($label->trackingNumber, 'UPS');

        $this->assertArrayHasKey('tracking_number', $tracking);
        $this->assertArrayHasKey('status', $tracking);
        $this->assertEquals($label->trackingNumber, $tracking['tracking_number']);
    }
}
