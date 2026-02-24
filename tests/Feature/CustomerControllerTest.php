<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    public function test_it_displays_customers_list()
    {
        Customer::factory()->count(3)->create();

        $response = $this->get(route('customers.index'));

        $response->assertOk();
        $response->assertViewIs('customers.index');
        $response->assertViewHas('customers');
    }

    public function test_it_shows_customer_with_order_count()
    {
        $customer = Customer::factory()->create(['name' => 'John Doe']);
        Order::factory()->count(3)->create(['customer_id' => $customer->id]);

        $response = $this->get(route('customers.index'));

        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('3 orders');
    }

    public function test_it_displays_customer_details()
    {
        $customer = Customer::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+1-555-0100'
        ]);

        $response = $this->get(route('customers.show', $customer->id));

        $response->assertOk();
        $response->assertViewIs('customers.show');
        $response->assertSee('Jane Smith');
        $response->assertSee('jane@example.com');
        $response->assertSee('+1-555-0100');
    }

    public function test_it_shows_customer_order_history()
    {
        $customer = Customer::factory()->create();
        $order1 = Order::factory()->create(['customer_id' => $customer->id]);
        $order2 = Order::factory()->create(['customer_id' => $customer->id]);

        $response = $this->get(route('customers.show', $customer->id));

        $response->assertOk();
        $response->assertSee('Order #' . $order1->id);
        $response->assertSee('Order #' . $order2->id);
    }

    public function test_it_redirects_when_customer_not_found()
    {
        $response = $this->get(route('customers.show', 999));

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('error', 'Customer not found');
    }
}
