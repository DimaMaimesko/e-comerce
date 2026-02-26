<?php

namespace App\Services\Shipping\Carriers;

use App\Services\Shipping\Carriers\ExternalAPIs\FedExAPI;
use App\Services\Shipping\Carriers\ValueObjects\Address;
use App\Services\Shipping\Carriers\ValueObjects\ShipmentLabel;
use App\Services\Shipping\Carriers\ValueObjects\TrackingInfo;
use DateTime;

class FedExAdapter implements ShippingCarrierInterface
{
    public function __construct(
        private FedExAPI $api
    ) {}

    public function calculateRate(Address $from, Address $to, float $weight): float
    {
        $response = $this->api->getRateQuote([
            'origin' => $from->toArray(),
            'destination' => $to->toArray(),
            'weight' => $weight,
        ]);

        return $response['total_charge'];
    }

    public function createShipment(Address $from, Address $to, float $weight, array $items): ShipmentLabel
    {
        $response = $this->api->createShippingLabel([
            'origin' => $from->toArray(),
            'destination' => $to->toArray(),
            'weight' => $weight,
            'items' => $items,
        ]);

        return new ShipmentLabel(
            trackingNumber: $response['tracking_id'],
            labelUrl: $response['label_image_url'],
            cost: $response['service_charge'],
            estimatedDays: 3,
            carrier: 'FedEx'
        );
    }

    public function trackPackage(string $trackingNumber): TrackingInfo
    {
        $response = $this->api->trackShipment($trackingNumber);

        return new TrackingInfo(
            trackingNumber: $response['tracking_id'],
            status: $response['status_description'],
            location: $response['current_location'],
            lastUpdate: new DateTime($response['last_scan_time']),
            estimatedDelivery: new DateTime($response['estimated_delivery']),
            events: $response['scan_events']
        );
    }

    public function cancelShipment(string $trackingNumber): bool
    {
        $response = $this->api->cancelShipment($trackingNumber);
        return $response['cancellation_status'] === 'SUCCESS';
    }

    public function getName(): string
    {
        return 'FedEx';
    }

    public function validateAddress(Address $address): bool
    {
        $response = $this->api->validateAddress($address->toArray());
        return $response['valid'];
    }
}
