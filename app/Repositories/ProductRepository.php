<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findAll(): Collection
    {
        return Product::all();
    }

    public function findInStock(): Collection
    {
        return Product::where('stock', '>', 0)->get();
    }

    public function save(Product $product): Product
    {
        $product->save();
        return $product;
    }

    public function delete(int $id): bool
    {
        return Product::destroy($id) > 0;
    }
}
