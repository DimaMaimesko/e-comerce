<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Services\Pricing\BasePriceCalculator;
use App\Services\Pricing\ConcreteDecorators\TaxDecorator;
use App\Services\Pricing\ConcreteDecorators\DiscountDecorator;
use App\Services\Pricing\ConcreteDecorators\SeasonalPriceDecorator;
use App\Services\Pricing\ConcreteDecorators\BulkDiscountDecorator;
use App\Services\Pricing\PricingService;

class PriceDecoratorTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
        ]);
    }

    public function test_base_price_calculator(): void
    {
        $calculator = new BasePriceCalculator();

        $price = $calculator->calculate($this->product, 2);

        $this->assertEquals(200.00, $price);
        $this->assertEquals('Base Price', $calculator->getDescription());
    }

    public function test_tax_decorator_adds_tax(): void
    {
        $calculator = new TaxDecorator(
            new BasePriceCalculator(),
            0.10 // 10% tax
        );

        $price = $calculator->calculate($this->product, 1);

        $this->assertEquals(110.00, $price);
        $this->assertStringContainsString('10% Tax', $calculator->getDescription());
    }

    public function test_discount_decorator_reduces_price(): void
    {
        $calculator = new DiscountDecorator(
            new BasePriceCalculator(),
            20 // 20% discount
        );

        $price = $calculator->calculate($this->product, 1);

        $this->assertEquals(80.00, $price);
        $this->assertStringContainsString('20% Discount', $calculator->getDescription());
    }

    public function test_seasonal_decorator_applies_multiplier(): void
    {
        $calculator = new SeasonalPriceDecorator(
            new BasePriceCalculator(),
            'Christmas',
            1.15 // 15% increase
        );

        $price = $calculator->calculate($this->product, 1);

        $this->assertEquals(115.00, $price);
        $this->assertStringContainsString('Christmas', $calculator->getDescription());
    }

    public function test_bulk_discount_applies_when_quantity_met(): void
    {
        $calculator = new BulkDiscountDecorator(
            new BasePriceCalculator(),
            10, // Min 10 items
            15  // 15% discount
        );

        $priceWithoutBulk = $calculator->calculate($this->product, 5);
        $priceWithBulk = $calculator->calculate($this->product, 10);

        $this->assertEquals(500.00, $priceWithoutBulk);
        $this->assertEquals(850.00, $priceWithBulk); // 1000 - 15%
    }

    public function test_stacking_multiple_decorators(): void
    {
        // 20% discount + 10% tax
        $calculator = new TaxDecorator(
            new DiscountDecorator(
                new BasePriceCalculator(),
                20 // 20% discount
            ),
            0.10 // 10% tax
        );

        $price = $calculator->calculate($this->product, 1);

        // 100 - 20% = 80, then 80 + 10% = 88
        $this->assertEquals(88.00, $price);
        $this->assertStringContainsString('Discount', $calculator->getDescription());
        $this->assertStringContainsString('Tax', $calculator->getDescription());
    }

    public function test_pricing_service_black_friday(): void
    {
        $service = new PricingService();

        $result = $service->calculateBlackFridayPrice($this->product, 10);

        // 100 * 10 = 1000
        // - 25% (Black Friday) = 750
        // - 15% (Bulk) = 637.50
        // + 10% (Tax) = 701.25
        $this->assertEquals(701.25, $result['price']);
        $this->assertArrayHasKey('description', $result);
    }

    public function test_pricing_service_custom_modifiers(): void
    {
        $service = new PricingService();

        $modifiers = [
            ['type' => 'discount', 'percentage' => 10],
            ['type' => 'tax', 'rate' => 0.08],
        ];

        $result = $service->calculateCustomPrice($this->product, 1, $modifiers);

        // 100 - 10% = 90, then 90 + 8% = 97.20
        $this->assertEquals(97.20, $result['price']);
    }
}
