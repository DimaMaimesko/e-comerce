<?php

namespace App\Services\OrderValidation;

use App\Exceptions\ValidationException;

class CustomerCreditValidator extends OrderValidator
{
    protected function check(array $orderData): void
    {
        $customer = $orderData['customer'];

        // Check if customer has a credit limit set
        if (!isset($customer->credit_limit)) {
            return; // No credit limit means no restriction
        }

        // Check if customer exceeded their credit limit
        $outstandingBalance = $customer->outstanding_balance ?? 0;

        if ($outstandingBalance > $customer->credit_limit) {
            throw new ValidationException(
                "Customer '{$customer->name}' has exceeded credit limit. Outstanding: \${$outstandingBalance}, Limit: \${$customer->credit_limit}"
            );
        }
    }
}
