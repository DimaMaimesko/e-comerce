<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');

// Helper functions
function createCustomer(array $attributes = [])
{
    return \App\Models\Customer::factory()->create($attributes);
}

function createProduct(array $attributes = [])
{
    return \App\Models\Product::factory()->create($attributes);
}

function createOrder(array $attributes = [])
{
    return \App\Models\Order::factory()->create($attributes);
}
