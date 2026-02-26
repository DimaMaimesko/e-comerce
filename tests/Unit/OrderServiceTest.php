<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\Shipping\StandardShipping;
use App\Events\OrderCreated;
use App\Events\OrderPaid;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    public function test_it_creates_order_successfully()
    {
        Event::fake();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 100, 'weight' => 2]);

        $items = [
            ['product' => $product, 'quantity' => 2]
        ];

        $service = app(OrderService::class);
        $order = $service->createOrder($customer, $items, new StandardShipping());

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertEquals(200.00, $order->total_amount);
        $this->assertGreaterThan(0, $order->shipping_cost);

        Event::assertDispatched(OrderCreated::class);
    }

//    public function test_it_throws_exception_when_insufficient_stock()
//    {
//        $this->expectException(\DomainException::class);
//
//        $customer = Customer::factory()->create();
//        $product = Product::factory()->create(['stock' => 1]);
//
//        $items = [
//            ['product' => $product, 'quantity' => 5]
//        ];
//
//        $service = app(OrderService::class);
//        $service->createOrder($customer, $items, new StandardShipping());
//    }

    public function test_it_processes_payment_successfully()
    {
        Event::fake();

        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $service = app(OrderService::class);
        $service->processPayment($order->id, 'stripe', []);

        $order->refresh();
        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertNotNull($order->payment_transaction_id);

        Event::assertDispatched(OrderPaid::class);
    }

    public function test_it_throws_exception_for_already_paid_order()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Order already paid');

        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);

        $service = app(OrderService::class);
        $service->processPayment($order->id, 'stripe', []);
    }
}
