<?php

namespace App\Services\Pricing;

use App\Models\Product;
use App\Services\Pricing\ConcreteDecorators\BulkDiscountDecorator;
use App\Services\Pricing\ConcreteDecorators\DiscountDecorator;
use App\Services\Pricing\ConcreteDecorators\SeasonalPriceDecorator;
use App\Services\Pricing\ConcreteDecorators\TaxDecorator;

class PricingService
{
    public function calculateWithTax(Product $product, int $quantity = 1, float $taxRate = 0.10): array
    {
        $calculator = new TaxDecorator(
            new BasePriceCalculator(),
            $taxRate
        );

        return [
            'price' => $calculator->calculate($product, $quantity),
            'description' => $calculator->getDescription(),
        ];
    }

    public function calculateWithDiscount(
        Product $product,
        int $quantity = 1,
        float $discountPercentage = 10
    ): array {
        $calculator = new DiscountDecorator(
            new BasePriceCalculator(),
            $discountPercentage
        );

        return [
            'price' => $calculator->calculate($product, $quantity),
            'description' => $calculator->getDescription(),
        ];
    }

    public function calculateBlackFridayPrice(Product $product, int $quantity = 1): array
    {
        // Stack multiple decorators: 25% off + bulk discount + tax
        $calculator = new TaxDecorator(
            new BulkDiscountDecorator(
                new DiscountDecorator(
                    new BasePriceCalculator(),
                    25 // 25% Black Friday discount
                ),
                10, // Bulk discount for 10+ items
                15  // 15% additional bulk discount
            ),
            0.10 // 10% tax
        );

        return [
            'price' => $calculator->calculate($product, $quantity),
            'description' => $calculator->getDescription(),
        ];
    }

    public function calculateCustomPrice(Product $product, int $quantity, array $modifiers): array
    {
        $calculator = new BasePriceCalculator();

        foreach ($modifiers as $modifier) {
            $calculator = match($modifier['type']) {
                'tax' => new TaxDecorator($calculator, $modifier['rate'] ?? 0.10),
                'discount' => new DiscountDecorator($calculator, $modifier['percentage']),
                'seasonal' => new SeasonalPriceDecorator(
                    $calculator,
                    $modifier['season'],
                    $modifier['multiplier']
                ),
                'bulk' => new BulkDiscountDecorator(
                    $calculator,
                    $modifier['min_quantity'] ?? 10,
                    $modifier['discount'] ?? 15
                ),
                default => $calculator,
            };
        }

        return [
            'price' => $calculator->calculate($product, $quantity),
            'description' => $calculator->getDescription(),
        ];
    }
}
