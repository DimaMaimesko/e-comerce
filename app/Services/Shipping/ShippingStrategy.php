<?php

namespace App\Services\Shipping;

use App\Models\Order;

interface ShippingStrategy
{
    public function calculateCost(Order $order): float;
    public function getEstimatedDays(): int;
    public function getName(): string;
}
