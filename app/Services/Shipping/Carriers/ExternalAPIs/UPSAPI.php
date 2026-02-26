<?php

namespace App\Services\Shipping\Carriers\ExternalAPIs;

/**
 * Mock UPS API
 */
class UPSAPI
{
    public function requestRate(array $request): array
    {
        // UPS uses different structure
        return [
            'RateResponse' => [
                'RatedShipment' => [
                    'Service' => ['Code' => '03'],
                    'TotalCharges' => [
                        'MonetaryValue' => $request['Package']['PackageWeight']['Weight'] * 1.3 + 8.00,
                    ],
                    'GuaranteedDelivery' => [
                        'BusinessDaysInTransit' => '2',
                    ],
                ],
            ],
        ];
    }

    public function createLabel(array $shipRequest): array
    {
        $trackingNumber = 'UPS_1Z' . strtoupper(substr(md5(uniqid()), 0, 16));

        return [
            'ShipmentResponse' => [
                'ShipmentResults' => [
                    'ShipmentIdentificationNumber' => $trackingNumber,
                    'PackageResults' => [
                        'TrackingNumber' => $trackingNumber,
                        'ShippingLabel' => [
                            'GraphicImage' => base64_encode('label_data'),
                            'HTMLImage' => "https://api.ups.com/labels/{$trackingNumber}.html",
                        ],
                    ],
                    'ShipmentCharges' => [
                        'TotalCharges' => [
                            'MonetaryValue' => $shipRequest['weight'] * 1.3 + 8.00,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function track(array $trackRequest): array
    {
        return [
            'TrackResponse' => [
                'Shipment' => [
                    'Package' => [
                        'TrackingNumber' => $trackRequest['InquiryNumber'],
                        'Activity' => [
                            [
                                'Status' => ['Description' => 'In Transit'],
                                'ActivityLocation' => ['Address' => ['City' => 'Louisville', 'StateProvinceCode' => 'KY']],
                                'Date' => date('Ymd'),
                                'Time' => date('His'),
                            ],
                        ],
                        'DeliveryDate' => [
                            'Date' => date('Ymd', strtotime('+2 days')),
                        ],
                    ],
                ],
            ],
        ];
    }

    public function voidShipment(array $voidRequest): array
    {
        return [
            'VoidShipmentResponse' => [
                'Response' => [
                    'ResponseStatus' => ['Code' => '1', 'Description' => 'Success'],
                ],
            ],
        ];
    }
}
