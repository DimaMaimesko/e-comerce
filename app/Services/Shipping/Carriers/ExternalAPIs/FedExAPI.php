<?php

namespace App\Services\Shipping\Carriers\ExternalAPIs;

/**
 * Mock FedEx API
 * In production, this would be their actual SDK/API client
 */
class FedExAPI
{
    public function getRateQuote(array $shipmentData): array
    {
        // Simulate API call
        return [
            'rate_id' => 'FEDEX_RATE_' . uniqid(),
            'service_type' => 'FEDEX_GROUND',
            'total_charge' => $shipmentData['weight'] * 1.5 + 10.00,
            'currency' => 'USD',
            'delivery_days' => 3,
        ];
    }

    public function createShippingLabel(array $shipmentData): array
    {
        // Simulate API call
        $trackingNumber = 'FEDEX_' . strtoupper(uniqid());

        return [
            'tracking_id' => $trackingNumber,
            'label_image_url' => "https://api.fedex.com/labels/{$trackingNumber}.pdf",
            'service_charge' => $shipmentData['weight'] * 1.5 + 10.00,
            'estimated_delivery_date' => date('Y-m-d', strtotime('+3 days')),
        ];
    }

    public function trackShipment(string $trackingId): array
    {
        return [
            'tracking_id' => $trackingId,
            'status_code' => 'IN_TRANSIT',
            'status_description' => 'Package is in transit',
            'current_location' => 'Memphis, TN',
            'last_scan_time' => date('Y-m-d H:i:s'),
            'estimated_delivery' => date('Y-m-d', strtotime('+2 days')),
            'scan_events' => [
                ['time' => date('Y-m-d H:i:s', strtotime('-2 hours')), 'location' => 'Memphis, TN', 'event' => 'Departed FedEx location'],
                ['time' => date('Y-m-d H:i:s', strtotime('-5 hours')), 'location' => 'Memphis, TN', 'event' => 'Arrived at FedEx location'],
            ],
        ];
    }

    public function cancelShipment(string $trackingId): array
    {
        return [
            'tracking_id' => $trackingId,
            'cancellation_status' => 'SUCCESS',
            'refund_amount' => 15.00,
        ];
    }

    public function validateAddress(array $addressData): array
    {
        return [
            'valid' => true,
            'corrected_address' => $addressData,
        ];
    }
}
