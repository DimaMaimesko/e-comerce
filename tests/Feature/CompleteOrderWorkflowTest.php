<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Events\OrderShipped;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CompleteOrderWorkflowTest extends TestCase
{
    public function test_complete_order_workflow_from_creation_to_shipping()
    {
        Event::fake();

        // Setup
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'stock' => 10,
            'price' => 100.00
        ]);

        // Step 1: Create Order
        $createResponse = $this->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'shipping_method' => 'express',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

        $createResponse->assertRedirect();
        $order = Order::latest()->first();

        Event::assertDispatched(OrderCreated::class);
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        $this->assertEquals(200.00, $order->total_amount);

        // Step 2: Process Payment
        $paymentResponse = $this->post(route('orders.payment.process', $order->id), [
            'payment_method' => 'stripe'
        ]);

        $paymentResponse->assertRedirect(route('orders.show', $order->id));
        $order->refresh();

        Event::assertDispatched(OrderPaid::class);
        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertNotNull($order->payment_transaction_id);

        // Step 3: Ship Order
        $shippingResponse = $this->post(route('orders.shipping.process', $order->id), [
            'carrier' => 'UPS'
        ]);

        $shippingResponse->assertRedirect(route('orders.show', $order->id));
        $order->refresh();

        Event::assertDispatched(OrderShipped::class);
        $this->assertEquals(Order::STATUS_SHIPPED, $order->status);
        $this->assertNotNull($order->tracking_number);
        $this->assertStringStartsWith('UPS_', $order->tracking_number);

        // Verify product stock decreased
        $product->refresh();
        $this->assertEquals(10, $product->stock);
    }

    public function test_order_workflow_respects_status_transitions()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        // Cannot ship before payment
        $response = $this->get(route('orders.shipping', $order->id));
        $response->assertRedirect(route('orders.show', $order->id));
        $response->assertSessionHas('error');

        // Process payment first
        $this->post(route('orders.payment.process', $order->id), [
            'payment_method' => 'paypal'
        ]);

        // Now shipping is allowed
        $response = $this->get(route('orders.shipping', $order->id));
        $response->assertOk();
    }

    public function test_cannot_pay_for_already_paid_order()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);

        $response = $this->get(route('orders.payment', $order->id));

        $response->assertRedirect(route('orders.show', $order->id));
        $response->assertSessionHas('info');
    }

    public function test_cannot_ship_already_shipped_order()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_SHIPPED]);

        $response = $this->get(route('orders.shipping', $order->id));

        $response->assertRedirect(route('orders.show', $order->id));
        $response->assertSessionHas('info');
    }
}
