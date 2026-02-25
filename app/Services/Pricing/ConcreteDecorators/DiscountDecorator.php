<?php

namespace App\Services\Pricing\ConcreteDecorators;

use App\Models\Product;
use App\Services\Pricing\PriceCalculatorInterface;
use App\Services\Pricing\PriceDecorator;

class DiscountDecorator extends PriceDecorator
{
    public function __construct(
        protected PriceCalculatorInterface $calculator,
        private float $discountPercentage
    ) {
        parent::__construct($calculator);
    }

    public function calculate(Product $product, int $quantity = 1): float
    {
        $basePrice = $this->calculator->calculate($product, $quantity);
        $discount = round($basePrice * ($this->discountPercentage / 100), 2);
        return $basePrice - $discount;
    }

    public function getDescription(): string
    {
        return $this->calculator->getDescription() . " - {$this->discountPercentage}% Discount";
    }
}
