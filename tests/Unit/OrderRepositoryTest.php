<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Customer;
use App\Repositories\OrderRepository;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    private OrderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new OrderRepository();
    }

    /** @test */
    public function test_it_finds_order_by_id()
    {
        $order = Order::factory()->create();

        $found = $this->repository->find($order->id);

        $this->assertNotNull($found);
        $this->assertEquals($order->id, $found->id);
    }

    /** @test */
    public function test_it_finds_orders_by_customer()
    {
        $customer = Customer::factory()->create();
        Order::factory()->count(2)->create(['customer_id' => $customer->id]);
        Order::factory()->create(); // Different customer

        $orders = $this->repository->findByCustomer($customer->id);

        $this->assertCount(2, $orders);
    }

    /** @test */
    public function test_it_finds_orders_by_status()
    {
        Order::factory()->count(2)->create(['status' => Order::STATUS_PENDING]);
        Order::factory()->create(['status' => Order::STATUS_PAID]);

        $pending = $this->repository->findByStatus(Order::STATUS_PENDING);

        $this->assertCount(2, $pending);
    }
}
