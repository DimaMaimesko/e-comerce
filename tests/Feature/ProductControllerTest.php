<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function test_it_displays_product_index_page()
    {
        Product::factory()->count(5)->create();

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }

    public function test_it_displays_only_products_with_stock_on_index()
    {
        $inStockProduct = Product::factory()->create(['stock' => 10, 'name' => 'Available Product']);
        $outOfStockProduct = Product::factory()->create(['stock' => 0, 'name' => 'Out of Stock Product']);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Available Product');
    }

    public function test_it_shows_product_details_page()
    {
        $product = Product::factory()->create([
            'name' => 'Test Laptop',
            'price' => 999.99,
            'stock' => 5,
            'weight' => 2.5
        ]);

        $response = $this->get(route('products.show', $product->id));

        $response->assertOk();
        $response->assertViewIs('products.show');
        $response->assertSee('Test Laptop');
        $response->assertSee('999.99');
        $response->assertSee('5 units');
    }

    public function test_it_redirects_when_product_not_found()
    {
        $response = $this->get(route('products.show', 999));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');
    }

    public function test_it_shows_out_of_stock_message_for_unavailable_products()
    {
        $product = Product::factory()->create(['stock' => 0]);

        $response = $this->get(route('products.show', $product->id));

        $response->assertOk();
        $response->assertSee('Out of Stock');
    }
}
