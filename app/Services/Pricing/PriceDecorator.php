<?php

namespace App\Services\Pricing;

use App\Models\Product;

abstract class PriceDecorator implements PriceCalculatorInterface
{
    public function __construct(
        protected PriceCalculatorInterface $calculator
    ) {}

    abstract public function calculate(Product $product, int $quantity = 1): float;

    abstract public function getDescription(): string;
}
