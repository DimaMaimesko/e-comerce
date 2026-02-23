<?php

namespace App\Services\Shipping;

use App\Models\Order;

class OvernightShipping implements ShippingStrategy
{
    private const BASE_COST = 30.00;
    private const COST_PER_KG = 5.00;

    public function calculateCost(Order $order): float
    {
        $weight = $order->getTotalWeight();
        return self::BASE_COST + ($weight * self::COST_PER_KG);
    }

    public function getEstimatedDays(): int
    {
        return 1;
    }

    public function getName(): string
    {
        return 'Overnight Shipping';
    }
}
