<?php

namespace App\Services\Pricing;

use App\Models\Product;

interface PriceCalculatorInterface
{
    public function calculate(Product $product, int $quantity = 1): float;
    public function getDescription(): string;
}
