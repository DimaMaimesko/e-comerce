<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;
    public function findByCustomer(int $customerId): Collection;
    public function findByStatus(string $status): Collection;
    public function save(Order $order): Order;
    public function delete(int $id): bool;
}
