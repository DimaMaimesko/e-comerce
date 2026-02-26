<?php

namespace App\Services\Shipping\Carriers;

use App\Services\Shipping\Carriers\ExternalAPI\UPSAPI;
use App\Services\Shipping\Carriers\ExternalAPIs\DHLAPI;
use App\Services\Shipping\Carriers\ValueObjects\Address;
use App\Services\Shipping\Carriers\ValueObjects\ShipmentLabel;
use App\Services\Shipping\Carriers\ValueObjects\TrackingInfo;
use DateTime;

class DHLAdapter implements ShippingCarrierInterface
{
    public function __construct(
        private DHLAPI $api
    ) {}

    public function calculateRate(Address $from, Address $to, float $weight): float
    {
        $request = [
            'customerDetails' => ['shipperDetails' => $from->toArray()],
            'plannedShippingDateAndTime' => ['weight' => $weight],
            'accounts' => [['typeCode' => 'shipper']],
        ];

        $response = $this->api->getRates($request);

        return $response['products'][0]['totalPrice']['price'];
    }

    public function createShipment(Address $from, Address $to, float $weight, array $items): ShipmentLabel
    {
        $request = [
            'shipper' => $from->toArray(),
            'receiver' => $to->toArray(),
            'weight' => $weight,
            'items' => $items,
        ];

        $response = $this->api->createShipment($request);

        return new ShipmentLabel(
            trackingNumber: $response['shipmentTrackingNumber'],
            labelUrl: $response['documents'][0]['url'],
            cost: $response['shipmentCharges'][0]['price'],
            estimatedDays: 2,
            carrier: 'DHL'
        );
    }

    public function trackPackage(string $trackingNumber): TrackingInfo
    {
        $response = $this->api->trackShipments([$trackingNumber]);
        $shipment = $response['shipments'][0];

        return new TrackingInfo(
            trackingNumber: $shipment['shipmentTrackingNumber'],
            status: $shipment['statusDescription'],
            location: $shipment['events'][0]['location']['address']['addressLocality'] ?? 'Unknown',
            lastUpdate: new DateTime($shipment['events'][0]['timestamp']),
            estimatedDelivery: null,
            events: array_map(fn($e) => [
                'description' => $e['description'],
                'location' => $e['location']['address']['addressLocality'] ?? 'Unknown',
                'time' => $e['timestamp'],
            ], $shipment['events'])
        );
    }

    public function cancelShipment(string $trackingNumber): bool
    {
        // DHL cancellation logic
        return true;
    }

    public function getName(): string
    {
        return 'DHL';
    }

    public function validateAddress(Address $address): bool
    {
        return true;
    }
}
