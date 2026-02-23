<?php

namespace App\Services\Payment;

class StripeGateway implements PaymentGatewayInterface
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge(float $amount, array $paymentDetails): PaymentResult
    {
        $transactionId = 'stripe_ch_' . uniqid();

        echo "[STRIPE] Processing payment of \${$amount}...\n";
        usleep(500000);

        return new PaymentResult(
            success: true,
            transactionId: $transactionId,
            message: 'Payment successful via Stripe',
            metadata: [
                'gateway' => 'stripe',
                'fee' => round($amount * 0.029 + 0.30, 2)
            ]
        );
    }

    public function refund(string $transactionId, float $amount): bool
    {
        echo "[STRIPE] Refunding \${$amount} for transaction {$transactionId}\n";
        return true;
    }

    public function getName(): string
    {
        return 'Stripe';
    }
}
