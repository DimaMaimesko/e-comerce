<?php

namespace App\Services\Shipping\Carriers;

use App\Services\Shipping\Carriers\ValueObjects\Address;
use App\Services\Shipping\Carriers\ValueObjects\ShipmentLabel;
use App\Services\Shipping\Carriers\ValueObjects\TrackingInfo;

interface ShippingCarrierInterface
{
    public function calculateRate(Address $from, Address $to, float $weight): float;

    public function createShipment(Address $from, Address $to, float $weight, array $items): ShipmentLabel;

    public function trackPackage(string $trackingNumber): TrackingInfo;

    public function cancelShipment(string $trackingNumber): bool;

    public function getName(): string;

    public function validateAddress(Address $address): bool;
}
