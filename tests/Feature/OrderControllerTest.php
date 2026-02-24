<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    public function test_it_displays_orders_list()
    {
        Order::factory()->count(3)->create();

        $response = $this->get(route('orders.index'));

        $response->assertOk();
        $response->assertViewIs('orders.index');
        $response->assertViewHas('orders');
    }

    public function test_it_creates_order_successfully()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'shipping_method' => 'standard',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => Order::STATUS_PENDING,
        ]);
    }

    public function test_it_validates_order_creation_input()
    {
        $response = $this->post(route('orders.store'), []);

        $response->assertSessionHasErrors(['customer_id', 'shipping_method', 'items']);
    }

    public function test_it_processes_payment_successfully()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $response = $this->post(route('orders.payment.process', $order->id), [
            'payment_method' => 'stripe'
        ]);

        $response->assertRedirect(route('orders.show', $order->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PAID,
        ]);
    }

    public function test_it_ships_order_successfully()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);

        $response = $this->post(route('orders.shipping.process', $order->id), [
            'carrier' => 'FedEx'
        ]);

        $response->assertRedirect(route('orders.show', $order->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_SHIPPED,
        ]);
    }
}
