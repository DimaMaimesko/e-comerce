<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\Shipping\StandardShipping;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class OrderPricingIntegrationTest extends TestCase
{
    public function test_order_creation_with_black_friday_pricing()
    {
        Event::fake();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 20, 'price' => 100, 'weight' => 2]);

        $items = [
            ['product' => $product, 'quantity' => 10]
        ];

        $modifiers = [
            ['type' => 'discount', 'percentage' => 25], // 25% off
            ['type' => 'bulk', 'min_quantity' => 10, 'discount' => 15], // 15% bulk off
            ['type' => 'tax', 'rate' => 0.10], // 10% tax
        ];

        // 100 * 10 = 1000
        // - 25% = 750
        // - 15% (bulk) = 637.50
        // + 10% (tax) = 701.25
        // Unit price = 70.125 -> round to 70.13

        $service = app(OrderService::class);
        $order = $service->createOrder($customer, $items, new StandardShipping(), $modifiers);

        $this->assertInstanceOf(Order::class, $order);

        // Total amount = unit_price * quantity
        // 70.13 * 10 = 701.30
        $this->assertEquals(701.30, $order->total_amount);

        $orderItem = $order->items[0];
        $this->assertEquals(70.13, $orderItem['price']);
        $this->assertStringContainsString('25% Discount', $orderItem['price_description']);
        $this->assertStringContainsString('Bulk', $orderItem['price_description']);
        $this->assertStringContainsString('10% Tax', $orderItem['price_description']);
    }

    public function test_order_creation_with_seasonal_pricing()
    {
        Event::fake();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 100, 'weight' => 2]);

        $items = [
            ['product' => $product, 'quantity' => 1]
        ];

        $modifiers = [
            ['type' => 'seasonal', 'season' => 'Christmas', 'multiplier' => 1.2], // 20% increase
        ];

        $service = app(OrderService::class);
        $order = $service->createOrder($customer, $items, new StandardShipping(), $modifiers);

        $this->assertEquals(120.00, $order->total_amount);
        $orderItem = $order->items[0];
        $this->assertStringContainsString('Christmas', $orderItem['price_description']);
    }
}
