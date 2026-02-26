<?php

namespace App\Services\OrderValidation;

use App\Exceptions\ValidationException;

class MinimumOrderValueValidator extends OrderValidator
{
    public function __construct(
        private float $minimumValue = 10.0
    ) {}

    protected function check(array $orderData): void
    {
        $total = 0;

        foreach ($orderData['items'] as $item) {
            $total += $item['product']->price * $item['quantity'];
        }

        if ($total < $this->minimumValue) {
            throw new ValidationException(
                "Order total (\${$total}) must be at least \${$this->minimumValue}"
            );
        }
    }
}
