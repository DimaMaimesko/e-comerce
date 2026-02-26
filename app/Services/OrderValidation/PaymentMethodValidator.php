<?php

namespace App\Services\OrderValidation;

use App\Exceptions\ValidationException;

class PaymentMethodValidator extends OrderValidator
{
    protected function check(array $orderData): void
    {
        $customer = $orderData['customer'];
        $paymentMethod = $orderData['payment_method'] ?? null;

        if (!$paymentMethod) {
            throw new ValidationException("Payment method is required");
        }

        // Validate specific payment methods
        if ($paymentMethod === 'credit_card') {
            if (!($customer->has_valid_credit_card ?? false)) {
                throw new ValidationException(
                    "Customer '{$customer->name}' has no valid credit card on file"
                );
            }
        }

        // Add more payment method validations as needed
    }
}
