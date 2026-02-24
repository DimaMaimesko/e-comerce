<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Events\OrderShipped;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ShippingCarriersTest extends TestCase
{
    public function test_it_ships_order_with_fedex()
    {
        Event::fake();
        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);

        $response = $this->post(route('orders.shipping.process', $order->id), [
            'carrier' => 'FedEx'
        ]);

        $response->assertRedirect(route('orders.show', $order->id));
        $order->refresh();

        $this->assertEquals(Order::STATUS_SHIPPED, $order->status);
        $this->assertStringStartsWith('FEDEX_', $order->tracking_number);

        Event::assertDispatched(OrderShipped::class);
    }

    public function test_it_ships_order_with_ups()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);

        $response = $this->post(route('orders.shipping.process', $order->id), [
            'carrier' => 'UPS'
        ]);

        $response->assertRedirect(route('orders.show', $order->id));
        $order->refresh();

        $this->assertStringStartsWith('UPS_', $order->tracking_number);
    }

    public function test_it_ships_order_with_dhl()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);

        $response = $this->post(route('orders.shipping.process', $order->id), [
            'carrier' => 'DHL'
        ]);

        $order->refresh();
        $this->assertStringStartsWith('DHL_', $order->tracking_number);
    }

    public function test_it_ships_order_with_usps()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);

        $response = $this->post(route('orders.shipping.process', $order->id), [
            'carrier' => 'USPS'
        ]);

        $order->refresh();
        $this->assertStringStartsWith('USPS_', $order->tracking_number);
    }
}
