<?php

namespace Tests\Unit;

use App\Models\Order;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    /** @test */
    public function test_it_calculates_grand_total_correctly()
    {
        $order = Order::factory()->make([
            'total_amount' => 100.00,
            'shipping_cost' => 15.50
        ]);

        $this->assertEquals(115.50, $order->getGrandTotal());
    }

    /** @test */
    public function test_it_calculates_total_weight()
    {
        $order = Order::factory()->make([
            'items' => [
                ['product_id' => 1, 'name' => 'Product 1', 'price' => 10, 'quantity' => 2, 'weight' => 1.5],
                ['product_id' => 2, 'name' => 'Product 2', 'price' => 20, 'quantity' => 1, 'weight' => 2.0],
            ]
        ]);

        $this->assertEquals(5.0, $order->getTotalWeight());
    }

    /** @test */
    public function test_it_knows_when_order_is_paid()
    {
        $paidOrder = Order::factory()->create(['status' => Order::STATUS_PAID]);
        $pendingOrder = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $this->assertTrue($paidOrder->isPaid());
        $this->assertFalse($pendingOrder->isPaid());
    }
}
