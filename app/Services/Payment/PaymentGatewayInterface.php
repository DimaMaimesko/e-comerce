<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function charge(float $amount, array $paymentDetails): PaymentResult;
    public function refund(string $transactionId, float $amount): bool;
    public function getName(): string;
}

