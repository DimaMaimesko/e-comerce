<?php

namespace App\Services\Pricing\ConcreteDecorators;

use App\Models\Product;
use App\Services\Pricing\PriceCalculatorInterface;
use App\Services\Pricing\PriceDecorator;

class BulkDiscountDecorator extends PriceDecorator
{
    public function __construct(
        protected PriceCalculatorInterface $calculator,
        private int $minQuantity = 10,
        private float $discountPercentage = 15
    ) {
        parent::__construct($calculator);
    }

    public function calculate(Product $product, int $quantity = 1): float
    {
        $basePrice = $this->calculator->calculate($product, $quantity);

        if ($quantity >= $this->minQuantity) {
            $discount = round($basePrice * ($this->discountPercentage / 100), 2);
            return $basePrice - $discount;
        }

        return $basePrice;
    }

    public function getDescription(): string
    {
        return $this->calculator->getDescription()
            . " (Bulk: {$this->discountPercentage}% off for {$this->minQuantity}+)";
    }
}
