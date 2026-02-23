<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Customers
        Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1-555-0100',
            'addresses' => ['123 Main St, New York, NY 10001']
        ]);

        Customer::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+1-555-0200',
            'addresses' => ['456 Oak Ave, Los Angeles, CA 90001']
        ]);

        // Create Products
        Product::create([
            'name' => 'Laptop Pro 15"',
            'price' => 1299.99,
            'stock' => 50,
            'weight' => 2.5
        ]);

        Product::create([
            'name' => 'Wireless Mouse',
            'price' => 29.99,
            'stock' => 200,
            'weight' => 0.2
        ]);

        Product::create([
            'name' => 'USB-C Hub',
            'price' => 49.99,
            'stock' => 100,
            'weight' => 0.3
        ]);

        Product::create([
            'name' => 'Laptop Bag',
            'price' => 79.99,
            'stock' => 75,
            'weight' => 0.8
        ]);
    }
}
