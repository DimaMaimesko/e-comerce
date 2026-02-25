<?php

namespace App\Services\Pricing;

use App\Models\Product;

class BasePriceCalculator implements PriceCalculatorInterface
{
    public function calculate(Product $product, int $quantity = 1): float
    {
        return round((float)$product->price * $quantity, 2);
    }

    public function getDescription(): string
    {
        return 'Base Price';
    }
}
