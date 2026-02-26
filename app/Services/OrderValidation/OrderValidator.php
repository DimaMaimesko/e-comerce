<?php

namespace App\Services\OrderValidation;

abstract class OrderValidator
{
    protected ?OrderValidator $nextValidator = null;

    /**
     * Set the next validator in the chain
     */
    public function setNext(OrderValidator $validator): OrderValidator
    {
        $this->nextValidator = $validator;
        return $validator;
    }

    /**
     * Validate the order data and pass to next validator
     */
    public function validate(array $orderData): void
    {
        // First, run this validator's check
        $this->check($orderData);

        // Then, pass to the next validator in the chain if it exists
        if ($this->nextValidator) {
            $this->nextValidator->validate($orderData);
        }
    }

    /**
     * Implement specific validation logic in concrete validators
     */
    abstract protected function check(array $orderData): void;
}
