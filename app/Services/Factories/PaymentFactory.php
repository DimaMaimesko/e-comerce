<?php

namespace App\Services\Factories;

use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\StripeGateway;
use App\Services\Payment\PayPalGateway;
use App\Services\Payment\CryptoGateway;

class PaymentFactory
{
    public function createPaymentGateway(string $type): PaymentGatewayInterface
    {
        $config = config('payment');

        return match(strtolower($type)) {
            'stripe' => new StripeGateway($config['stripe']['api_key']),
            'paypal' => new PayPalGateway(
                $config['paypal']['client_id'],
                $config['paypal']['client_secret']
            ),
            'crypto' => new CryptoGateway($config['crypto']['wallet_address']),
            default => throw new \InvalidArgumentException(
                "Unsupported payment gateway: $type"
            )
        };
    }

    public function getSupportedGateways(): array
    {
        return ['stripe', 'paypal', 'crypto'];
    }
}
