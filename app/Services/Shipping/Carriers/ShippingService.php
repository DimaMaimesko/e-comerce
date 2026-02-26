<?php

namespace App\Services\Shipping\Carriers;

use App\Models\Order;
use App\Services\Shipping\Carriers\ShippingCarrierFactory;
use App\Services\Shipping\Carriers\ValueObjects\Address;
use App\Services\Shipping\Carriers\ValueObjects\ShipmentLabel;

class ShippingService
{
    public function __construct(
        private ShippingCarrierFactory $carrierFactory
    ) {}

    public function createShipment(Order $order, string $carrierName): ShipmentLabel
    {
        $carrier = $this->carrierFactory->create($carrierName);

        // Create addresses from order data
        $from = new Address(
            street: '123 Warehouse St',
            city: 'Los Angeles',
            state: 'CA',
            zipCode: '90001'
        );

        $to = new Address(
            street: $order->customer->address ?? '456 Customer Ave',
            city: $order->customer->city ?? 'New York',
            state: $order->customer->state ?? 'NY',
            zipCode: $order->customer->zip_code ?? '10001'
        );

        $weight = $order->getTotalWeight();
        $items = $order->items;

        return $carrier->createShipment($from, $to, $weight, $items);
    }

    public function compareRates(Order $order): array
    {
        $carriers = $this->carrierFactory->getAllCarriers();
        $rates = [];

        $from = new Address('123 Warehouse St', 'Los Angeles', 'CA', '90001');
        $to = new Address('456 Customer Ave', 'New York', 'NY', '10001');
        $weight = $order->getTotalWeight();

        foreach ($carriers as $name => $carrier) {
            try {
                $rates[$name] = [
                    'carrier' => $name,
                    'rate' => $carrier->calculateRate($from, $to, $weight),
                ];
            } catch (\Exception $e) {
                $rates[$name] = [
                    'carrier' => $name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Sort by rate
        uasort($rates, fn($a, $b) => ($a['rate'] ?? PHP_FLOAT_MAX) <=> ($b['rate'] ?? PHP_FLOAT_MAX));

        return $rates;
    }

    public function trackShipment(string $trackingNumber, string $carrierName): array
    {
        $carrier = $this->carrierFactory->create($carrierName);
        $tracking = $carrier->trackPackage($trackingNumber);

        return $tracking->toArray();
    }
}
