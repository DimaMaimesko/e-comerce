<?php

namespace App\Services\Shipping\Carriers;

use App\Services\Shipping\Carriers\ExternalAPIs\FedExAPI;
use App\Services\Shipping\Carriers\ExternalAPIs\UPSAPI;
use App\Services\Shipping\Carriers\ExternalAPIs\DHLAPI;

class ShippingCarrierFactory
{
    public function create(string $carrierName): ShippingCarrierInterface
    {
        return match(strtoupper($carrierName)) {
            'FEDEX' => new FedExAdapter(new FedExAPI()),
            'UPS' => new UPSAdapter(new UPSAPI()),
            'DHL' => new DHLAdapter(new DHLAPI()),
            default => throw new \InvalidArgumentException("Unsupported carrier: {$carrierName}")
        };
    }

    public function getAllCarriers(): array
    {
        return [
            'FedEx' => $this->create('FEDEX'),
            'UPS' => $this->create('UPS'),
            'DHL' => $this->create('DHL'),
        ];
    }

    public function getSupportedCarriers(): array
    {
        return ['FEDEX', 'UPS', 'DHL'];
    }
}
