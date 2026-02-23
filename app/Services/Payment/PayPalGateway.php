<?php

namespace App\Services\Payment;

class PayPalGateway implements PaymentGatewayInterface
{
    private string $clientId;
    private string $clientSecret;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function charge(float $amount, array $paymentDetails): PaymentResult
    {
        $transactionId = 'paypal_' . uniqid();

        echo "[PAYPAL] Processing payment of \${$amount}...\n";
        usleep(700000);

        return new PaymentResult(
            success: true,
            transactionId: $transactionId,
            message: 'Payment successful via PayPal',
            metadata: [
                'gateway' => 'paypal',
                'fee' => round($amount * 0.034 + 0.30, 2)
            ]
        );
    }

    public function refund(string $transactionId, float $amount): bool
    {
        echo "[PAYPAL] Refunding \${$amount} for transaction {$transactionId}\n";
        return true;
    }

    public function getName(): string
    {
        return 'PayPal';
    }
}
