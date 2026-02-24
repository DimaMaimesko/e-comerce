<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Events\OrderPaid;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PaymentMethodsTest extends TestCase
{
    public function test_it_processes_stripe_payment()
    {
        Event::fake();
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $response = $this->post(route('orders.payment.process', $order->id), [
            'payment_method' => 'stripe'
        ]);

        $response->assertRedirect(route('orders.show', $order->id));
        $order->refresh();

        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertStringStartsWith('stripe_ch_', $order->payment_transaction_id);

        Event::assertDispatched(OrderPaid::class, function ($event) use ($order) {
            return $event->paymentMethod === 'stripe' && $event->order->id === $order->id;
        });
    }

    public function test_it_processes_paypal_payment()
    {
        Event::fake();
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $response = $this->post(route('orders.payment.process', $order->id), [
            'payment_method' => 'paypal'
        ]);

        $response->assertRedirect(route('orders.show', $order->id));
        $order->refresh();

        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertStringStartsWith('paypal_', $order->payment_transaction_id);

        Event::assertDispatched(OrderPaid::class, function ($event) {
            return $event->paymentMethod === 'paypal';
        });
    }

    public function test_it_processes_crypto_payment()
    {
        Event::fake();
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $response = $this->post(route('orders.payment.process', $order->id), [
            'payment_method' => 'crypto'
        ]);

        $response->assertRedirect(route('orders.show', $order->id));
        $order->refresh();

        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertStringStartsWith('crypto_', $order->payment_transaction_id);

        Event::assertDispatched(OrderPaid::class, function ($event) {
            return $event->paymentMethod === 'crypto';
        });
    }
}
