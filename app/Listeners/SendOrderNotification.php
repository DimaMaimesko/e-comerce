<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Events\OrderShipped;
use App\Services\Factories\NotificationFactory;

class SendOrderNotification
{
    public function __construct(
        private NotificationFactory $notificationFactory
    ) {}

    public function handle(OrderCreated|OrderPaid|OrderShipped $event): void
    {
        $email = $this->notificationFactory->createNotification('email');
        $order = $event->order;
        $customer = $order->customer;

        if ($event instanceof OrderCreated) {
            $email->send(
                $customer->email,
                'Order Confirmation - #' . $order->id,
                "Thank you for your order! Your order #{$order->id} " .
                "has been received. Total: $" . $order->getGrandTotal()
            );
        }

        if ($event instanceof OrderPaid) {
            $email->send(
                $customer->email,
                'Payment Received - Order #' . $order->id,
                "Your payment has been received. Transaction ID: " .
                $event->transactionId . ". We're preparing your order for shipment."
            );
        }

        if ($event instanceof OrderShipped) {
            $email->send(
                $customer->email,
                'Order Shipped - #' . $order->id,
                "Your order has been shipped! Tracking: " .
                $event->trackingNumber . " via " . $event->carrier
            );
        }
    }
}
