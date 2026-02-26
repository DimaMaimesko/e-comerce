<?php

namespace App\Services\OrderValidation;

use App\Exceptions\ValidationException;

class StockValidator extends OrderValidator
{
    protected function check(array $orderData): void
    {
        foreach ($orderData['items'] as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            if (!$product->isInStock($quantity)) {
                throw new ValidationException(
                    "Product '{$product->name}' has insufficient stock. Available: {$product->stock}, Requested: {$quantity}"
                );
            }
        }
    }
}
