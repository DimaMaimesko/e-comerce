<?php

namespace App\Services\Shipping\Carriers;

use App\Services\Shipping\Carriers\ExternalAPIs\UPSAPI;
use App\Services\Shipping\Carriers\ValueObjects\Address;
use App\Services\Shipping\Carriers\ValueObjects\ShipmentLabel;
use App\Services\Shipping\Carriers\ValueObjects\TrackingInfo;
use DateTime;

class UPSAdapter implements ShippingCarrierInterface
{
    public function __construct(
        private UPSAPI $api
    ) {}

    public function calculateRate(Address $from, Address $to, float $weight): float
    {
        // Adapt our format to UPS format
        $request = [
            'Shipment' => [
                'Shipper' => ['Address' => $from->toArray()],
                'ShipTo' => ['Address' => $to->toArray()],
                'Package' => [
                    'PackageWeight' => [
                        'Weight' => $weight,
                        'UnitOfMeasurement' => ['Code' => 'LBS'],
                    ],
                ],
            ],
        ];

        $response = $this->api->requestRate($request);

        return $response['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'];
    }

    public function createShipment(Address $from, Address $to, float $weight, array $items): ShipmentLabel
    {
        $request = [
            'shipper' => $from->toArray(),
            'shipTo' => $to->toArray(),
            'weight' => $weight,
            'items' => $items,
        ];

        $response = $this->api->createLabel($request);
        $results = $response['ShipmentResponse']['ShipmentResults'];

        return new ShipmentLabel(
            trackingNumber: $results['PackageResults']['TrackingNumber'],
            labelUrl: $results['PackageResults']['ShippingLabel']['HTMLImage'],
            cost: $results['ShipmentCharges']['TotalCharges']['MonetaryValue'],
            estimatedDays: 2,
            carrier: 'UPS'
        );
    }

    public function trackPackage(string $trackingNumber): TrackingInfo
    {
        $response = $this->api->track(['InquiryNumber' => $trackingNumber]);
        $shipment = $response['TrackResponse']['Shipment']['Package'];
        $activity = $shipment['Activity'][0];

        return new TrackingInfo(
            trackingNumber: $trackingNumber,
            status: $activity['Status']['Description'],
            location: $activity['ActivityLocation']['Address']['City'] . ', ' .
            $activity['ActivityLocation']['Address']['StateProvinceCode'],
            lastUpdate: DateTime::createFromFormat('YmdHis', $activity['Date'] . $activity['Time']),
            estimatedDelivery: DateTime::createFromFormat('Ymd', $shipment['DeliveryDate']['Date']),
            events: array_map(fn($a) => [
                'description' => $a['Status']['Description'],
                'location' => $a['ActivityLocation']['Address']['City'] ?? 'Unknown',
                'time' => $a['Date'] . ' ' . $a['Time'],
            ], $shipment['Activity'])
        );
    }

    public function cancelShipment(string $trackingNumber): bool
    {
        $response = $this->api->voidShipment(['ShipmentIdentificationNumber' => $trackingNumber]);
        return $response['VoidShipmentResponse']['Response']['ResponseStatus']['Code'] === '1';
    }

    public function getName(): string
    {
        return 'UPS';
    }

    public function validateAddress(Address $address): bool
    {
        // UPS address validation would go here
        return true;
    }
}
