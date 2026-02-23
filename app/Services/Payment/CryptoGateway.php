<?php

namespace App\Services\Payment;

class CryptoGateway implements PaymentGatewayInterface
{
    private string $walletAddress;

    public function __construct(string $walletAddress)
    {
        $this->walletAddress = $walletAddress;
    }

    public function charge(float $amount, array $paymentDetails): PaymentResult
    {
        $transactionId = 'crypto_' . bin2hex(random_bytes(16));

        echo "[CRYPTO] Processing payment of \${$amount}...\n";
        usleep(1000000);

        return new PaymentResult(
            success: true,
            transactionId: $transactionId,
            message: 'Payment successful via Cryptocurrency',
            metadata: [
                'gateway' => 'crypto',
                'fee' => round($amount * 0.01, 2),
                'network' => 'Bitcoin'
            ]
        );
    }

    public function refund(string $transactionId, float $amount): bool
    {
        echo "[CRYPTO] Refunding \${$amount} for transaction {$transactionId}\n";
        return true;
    }

    public function getName(): string
    {
        return 'Cryptocurrency';
    }
}
