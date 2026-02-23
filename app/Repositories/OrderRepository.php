<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function find(int $id): ?Order
    {
        return Order::with('customer')->find($id);
    }

    public function findByCustomer(int $customerId): Collection
    {
        return Order::where('customer_id', $customerId)->get();
    }

    public function findByStatus(string $status): Collection
    {
        return Order::where('status', $status)->get();
    }

    public function save(Order $order): Order
    {
        $order->save();
        return $order;
    }

    public function delete(int $id): bool
    {
        return Order::destroy($id) > 0;
    }
}
