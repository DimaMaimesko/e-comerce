<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'items' => [
                [
                    'product_id' => 1,
                    'name' => 'Sample Product',
                    'price' => 100.00,
                    'quantity' => 1,
                    'weight' => 1.5,
                ]
            ],
            'status' => Order::STATUS_PENDING,
            'total_amount' => 100.00,
            'shipping_cost' => 10.00,
            'shipping_method' => 'Standard Shipping',
        ];
    }
}
