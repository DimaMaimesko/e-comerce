<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Shipping\ShippingStrategy;
use App\Services\Factories\PaymentFactory;
use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Events\OrderShipped;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductRepositoryInterface $productRepository,
        private PaymentFactory $paymentFactory
    ) {}

    public function createOrder(
        Customer $customer,
        array $items,
        ShippingStrategy $shippingStrategy
    ): Order {
        // Validate stock availability
        foreach ($items as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            if (!$product->isInStock($quantity)) {
                throw new \DomainException(
                    "Product {$product->name} has insufficient stock"
                );
            }
        }

        // Prepare items for storage
        $orderItems = array_map(function($item) {
            return [
                'product_id' => $item['product']->id,
                'name' => $item['product']->name,
                'price' => $item['product']->price,
                'quantity' => $item['quantity'],
                'weight' => $item['product']->weight,
            ];
        }, $items);

        // Create order
        $order = new Order([
            'customer_id' => $customer->id,
            'items' => $orderItems,
            'shipping_method' => $shippingStrategy->getName(),
        ]);

        $order->save();
        $order->calculateTotal();

        // Calculate and set shipping cost using Strategy Pattern
        $shippingCost = $shippingStrategy->calculateCost($order);
        $order->shipping_cost = $shippingCost;
        $order->save();

        echo "\n┌─────────────────────────────────────────────┐\n";
        echo "│  ORDER CREATED: #{$order->id}\n";
        echo "├─────────────────────────────────────────────┤\n";
        echo "│  Customer: {$customer->name}\n";
        echo "│  Items: " . count($items) . "\n";
        echo "│  Subtotal: \$" . number_format($order->total_amount, 2) . "\n";
        echo "│  Shipping: \$" . number_format($shippingCost, 2) .
            " ({$shippingStrategy->getName()})\n";
        echo "│  Grand Total: \$" . number_format($order->getGrandTotal(), 2) . "\n";
        echo "└─────────────────────────────────────────────┘\n";

        // Save order
        $this->orderRepository->save($order);

        // Dispatch event using Observer Pattern
        event(new OrderCreated($order));

        return $order;
    }

    public function processPayment(
        int $orderId,
        string $paymentMethod,
        array $paymentDetails
    ): void {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new \DomainException("Order not found: $orderId");
        }

        if ($order->isPaid()) {
            throw new \DomainException("Order already paid: $orderId");
        }

        // Create payment gateway using Factory Pattern
        $gateway = $this->paymentFactory->createPaymentGateway($paymentMethod);

        echo "\n┌─────────────────────────────────────────────┐\n";
        echo "│  PROCESSING PAYMENT\n";
        echo "├─────────────────────────────────────────────┤\n";
        echo "│  Order: #{$order->id}\n";
        echo "│  Amount: \$" . number_format($order->getGrandTotal(), 2) . "\n";
        echo "│  Gateway: {$gateway->getName()}\n";
        echo "└─────────────────────────────────────────────┘\n\n";

        // Process payment
        $result = $gateway->charge($order->getGrandTotal(), $paymentDetails);

        if ($result->success) {
            $order->status = Order::STATUS_PAID;
            $order->payment_transaction_id = $result->transactionId;
            $this->orderRepository->save($order);

            echo "✓ Payment successful! Transaction: {$result->transactionId}\n";

            // Dispatch event
            event(new OrderPaid($order, $result->transactionId, $paymentMethod));
        } else {
            throw new \RuntimeException("Payment failed: {$result->message}");
        }
    }

    public function shipOrder(int $orderId, string $carrier): void
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new \DomainException("Order not found: $orderId");
        }

        if (!$order->isPaid()) {
            throw new \DomainException("Order must be paid before shipping: $orderId");
        }

        $trackingNumber = strtoupper($carrier) . '_' . uniqid();
        $order->tracking_number = $trackingNumber;
        $order->status = Order::STATUS_SHIPPED;
        $this->orderRepository->save($order);

        echo "\n┌─────────────────────────────────────────────┐\n";
        echo "│  ORDER SHIPPED\n";
        echo "├─────────────────────────────────────────────┤\n";
        echo "│  Order: #{$order->id}\n";
        echo "│  Carrier: {$carrier}\n";
        echo "│  Tracking: {$trackingNumber}\n";
        echo "└─────────────────────────────────────────────┘\n";

        // Dispatch event
        event(new OrderShipped($order, $trackingNumber, $carrier));
    }

    public function getOrder(int $orderId): ?Order
    {
        return $this->orderRepository->find($orderId);
    }

    public function getCustomerOrders(int $customerId): \Illuminate\Support\Collection
    {
        return $this->orderRepository->findByCustomer($customerId);
    }
}
