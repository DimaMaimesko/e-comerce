<?php

namespace App\Services\Pricing\ConcreteDecorators;

use App\Services\Pricing\PriceCalculatorInterface;
use App\Services\Pricing\PriceDecorator;
use App\Models\Product;

class TaxDecorator extends PriceDecorator
{
    public function __construct(
        protected PriceCalculatorInterface $calculator,
        private float $taxRate = 0.10 // 10% default
    ) {
        parent::__construct($calculator);
    }

    public function calculate(Product $product, int $quantity = 1): float
    {
        $basePrice = $this->calculator->calculate($product, $quantity);
        return round($basePrice + ($basePrice * $this->taxRate), 2);
    }

    public function getDescription(): string
    {
        $percentage = $this->taxRate * 100;
        return $this->calculator->getDescription() . " + {$percentage}% Tax";
    }
}
