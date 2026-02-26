<?php

namespace App\Services\Shipping\Carriers\ValueObjects;

readonly class ShipmentLabel
{
    public function __construct(
        public string $trackingNumber,
        public string $labelUrl,
        public float $cost,
        public int $estimatedDays,
        public string $carrier
    ) {}

    public function toArray(): array
    {
        return [
            'tracking_number' => $this->trackingNumber,
            'label_url' => $this->labelUrl,
            'cost' => $this->cost,
            'estimated_days' => $this->estimatedDays,
            'carrier' => $this->carrier,
        ];
    }
}
