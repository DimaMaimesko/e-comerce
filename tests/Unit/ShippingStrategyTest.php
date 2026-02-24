<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\Shipping\StandardShipping;
use App\Services\Shipping\ExpressShipping;
use App\Services\Shipping\OvernightShipping;
use Tests\TestCase;

class ShippingStrategyTest extends TestCase
{
    public function test_standard_shipping_calculates_cost_correctly()
    {
        $order = Order::factory()->make([
            'items' => [
                ['product_id' => 1, 'name' => 'Product', 'price' => 10, 'quantity' => 1, 'weight' => 2.5],
            ]
        ]);

        $strategy = new StandardShipping();

        $this->assertEquals(10.00, $strategy->calculateCost($order));
        $this->assertEquals(7, $strategy->getEstimatedDays());
    }

    public function test_express_shipping_calculates_cost_correctly()
    {
        $order = Order::factory()->make([
            'items' => [
                ['product_id' => 1, 'name' => 'Product', 'price' => 10, 'quantity' => 1, 'weight' => 3.0],
            ]
        ]);

        $strategy = new ExpressShipping();

        $this->assertEquals(25.50, $strategy->calculateCost($order));
        $this->assertEquals(3, $strategy->getEstimatedDays());
    }

    public function test_overnight_shipping_calculates_cost_correctly()
    {
        $order = Order::factory()->make([
            'items' => [
                ['product_id' => 1, 'name' => 'Product', 'price' => 10, 'quantity' => 1, 'weight' => 1.5],
            ]
        ]);

        $strategy = new OvernightShipping();

        $this->assertEquals(37.50, $strategy->calculateCost($order));
        $this->assertEquals(1, $strategy->getEstimatedDays());
    }
}
