<?php

namespace App\Services\Shipping\Carriers\ValueObjects;

use DateTime;

readonly class TrackingInfo
{
    public function __construct(
        public string $trackingNumber,
        public string $status,
        public string $location,
        public DateTime $lastUpdate,
        public ?DateTime $estimatedDelivery = null,
        public array $events = []
    ) {}

    public function toArray(): array
    {
        return [
            'tracking_number' => $this->trackingNumber,
            'status' => $this->status,
            'location' => $this->location,
            'last_update' => $this->lastUpdate->format('Y-m-d H:i:s'),
            'estimated_delivery' => $this->estimatedDelivery?->format('Y-m-d'),
            'events' => $this->events,
        ];
    }
}
