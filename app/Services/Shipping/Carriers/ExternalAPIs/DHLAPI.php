<?php

namespace App\Services\Shipping\Carriers\ExternalAPIs;

/**
 * Mock DHL API
 */
class DHLAPI
{
    public function getRates(array $rateRequest): array
    {
        return [
            'products' => [
                [
                    'productCode' => 'P',
                    'productName' => 'DHL Express',
                    'totalPrice' => [
                        'price' => $rateRequest['plannedShippingDateAndTime']['weight'] * 2.0 + 12.00,
                        'currency' => 'USD',
                    ],
                    'deliveryTime' => '2',
                ],
            ],
        ];
    }

    public function createShipment(array $shipmentRequest): array
    {
        $trackingNumber = 'DHL_' . rand(1000000000, 9999999999);

        return [
            'shipmentTrackingNumber' => $trackingNumber,
            'documents' => [
                [
                    'typeCode' => 'label',
                    'imageFormat' => 'PDF',
                    'content' => base64_encode('dhl_label'),
                    'url' => "https://api.dhl.com/labels/{$trackingNumber}.pdf",
                ],
            ],
            'shipmentCharges' => [
                [
                    'currencyType' => 'USD',
                    'priceCurrency' => 'USD',
                    'price' => $shipmentRequest['weight'] * 2.0 + 12.00,
                ],
            ],
        ];
    }

    public function trackShipments(array $trackingNumbers): array
    {
        return [
            'shipments' => array_map(function($tn) {
                return [
                    'shipmentTrackingNumber' => $tn,
                    'status' => 'transit',
                    'statusDescription' => 'Shipment is on the way',
                    'events' => [
                        [
                            'timestamp' => date('c'),
                            'location' => ['address' => ['addressLocality' => 'Hong Kong']],
                            'description' => 'Processed at DHL facility',
                        ],
                    ],
                ];
            }, $trackingNumbers),
        ];
    }
}
