<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Repositories\Contracts\ProductRepositoryInterface;

class UpdateInventory
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        echo "[INVENTORY] Updating stock for order #{$order->id}...\n";

        foreach ($order->items as $item) {
            $product = $this->productRepository->find($item['product_id']);

            if ($product) {
                $product->decreaseStock($item['quantity']);
                $this->productRepository->save($product);

                echo "[INVENTORY] - {$product->name}: -{$item['quantity']} " .
                    "(Stock remaining: {$product->stock})\n";
            }
        }

        echo "[INVENTORY] Stock updated successfully\n\n";
    }
}
