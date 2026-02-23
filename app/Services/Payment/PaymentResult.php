<?php

namespace App\Services\Payment;
class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $transactionId,
        public string $message,
        public ?array $metadata = null
    ) {}
}
