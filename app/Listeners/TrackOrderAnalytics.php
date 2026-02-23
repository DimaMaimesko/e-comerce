<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\OrderPaid;
use Illuminate\Support\Facades\Cache;

class TrackOrderAnalytics
{
    public function handle(OrderCreated|OrderPaid $event): void
    {
        if ($event instanceof OrderCreated) {
            $this->trackOrderCreated($event);
        }

        if ($event instanceof OrderPaid) {
            $this->trackOrderPaid($event);
        }
    }

    private function trackOrderCreated(OrderCreated $event): void
    {
        $order = $event->order;

        Cache::increment('metrics.total_orders');

        echo "[ANALYTICS] Order created tracked\n";
        echo "[ANALYTICS] - Order ID: {$order->id}\n";
        echo "[ANALYTICS] - Customer: {$order->customer->name}\n";
        echo "[ANALYTICS] - Total Orders: " . Cache::get('metrics.total_orders', 0) . "\n\n";
    }

    private function trackOrderPaid(OrderPaid $event): void
    {
        $order = $event->order;
        $amount = $order->getGrandTotal();

        Cache::increment('metrics.paid_orders');
        Cache::increment('metrics.total_revenue', $amount);

        echo "[ANALYTICS] Payment tracked\n";
        echo "[ANALYTICS] - Amount: \${$amount}\n";
        echo "[ANALYTICS] - Method: {$event->paymentMethod}\n";
        echo "[ANALYTICS] - Total Revenue: \$" .
            round(Cache::get('metrics.total_revenue', 0), 2) . "\n\n";
    }
}
