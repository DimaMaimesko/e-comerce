<?php

namespace App\Services\Shipping;

use App\Models\Order;

class ExpressShipping implements ShippingStrategy
{
    private const BASE_COST = 15.00;
    private const COST_PER_KG = 3.50;

    public function calculateCost(Order $order): float
    {
        $weight = $order->getTotalWeight();
        return self::BASE_COST + ($weight * self::COST_PER_KG);
    }

    public function getEstimatedDays(): int
    {
        return 3;
    }

    public function getName(): string
    {
        return 'Express Shipping';
    }
}
