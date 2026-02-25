<?php

namespace App\Services\Pricing\ConcreteDecorators;

use App\Models\Product;
use App\Services\Pricing\PriceCalculatorInterface;
use App\Services\Pricing\PriceDecorator;

class SeasonalPriceDecorator extends PriceDecorator
{
    public function __construct(
        protected PriceCalculatorInterface $calculator,
        private string $seasonName,
        private float $multiplier
    ) {
        parent::__construct($calculator);
    }

    public function calculate(Product $product, int $quantity = 1): float
    {
        $basePrice = $this->calculator->calculate($product, $quantity);
        return round($basePrice * $this->multiplier, 2);
    }

    public function getDescription(): string
    {
        $change = ($this->multiplier - 1) * 100;
        $sign = $change > 0 ? '+' : '';
        return $this->calculator->getDescription() . " {$sign}{$change}% ({$this->seasonName})";
    }
}
