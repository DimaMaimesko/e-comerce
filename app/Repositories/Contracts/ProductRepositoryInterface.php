<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function find(int $id): ?Product;
    public function findAll(): Collection;
    public function findInStock(): Collection;
    public function save(Product $product): Product;
    public function delete(int $id): bool;
}
