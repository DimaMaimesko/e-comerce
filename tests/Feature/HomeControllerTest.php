<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Customer;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function test_it_displays_home_page()
    {

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('home');
    }

    public function test_it_shows_statistics_on_home_page()
    {
        Product::factory()->count(5)->create();
        Customer::factory()->count(3)->create();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('totalProducts', 5);
        $response->assertViewHas('totalCustomers', 3);
    }

    public function test_it_shows_featured_products()
    {
        $products = Product::factory()->count(4)->create(['stock' => 10]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('products');

        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    public function test_it_shows_design_pattern_information()
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Strategy Pattern');
        $response->assertSee('Factory Pattern');
        $response->assertSee('Repository Pattern');
        $response->assertSee('Observer Pattern');
    }
}
