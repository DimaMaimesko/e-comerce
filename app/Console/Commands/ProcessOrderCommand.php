<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\Shipping\StandardShipping;
use App\Services\Shipping\ExpressShipping;
use App\Services\Shipping\OvernightShipping;

class ProcessOrderCommand extends Command
{
    protected $signature = 'order:demo';
    protected $description = 'Run order processing demo with all design patterns';

    public function __construct(
        private OrderService $orderService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info("╔═══════════════════════════════════════════════════════╗");
        $this->info("║                                                       ║");
        $this->info("║       E-COMMERCE ORDER PROCESSING SYSTEM              ║");
        $this->info("║       Demonstrating 4 Design Patterns                 ║");
        $this->info("║                                                       ║");
        $this->info("╚═══════════════════════════════════════════════════════╝\n");

        // Get data
        $customer1 = Customer::where('email', 'john@example.com')->first();
        $customer2 = Customer::where('email', 'jane@example.com')->first();

        $product1 = Product::where('name', 'Laptop Pro 15"')->first();
        $product2 = Product::where('name', 'Wireless Mouse')->first();
        $product3 = Product::where('name', 'USB-C Hub')->first();
        $product4 = Product::where('name', 'Laptop Bag')->first();

        $this->info("═══════════════════════════════════════════════════════\n");

        // SCENARIO 1: Standard Shipping with Stripe
        $this->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->info("║  SCENARIO 1: Standard Order with Stripe              ║");
        $this->info("╚═══════════════════════════════════════════════════════╝\n");

        $items1 = [
            ['product' => $product1, 'quantity' => 1],
            ['product' => $product2, 'quantity' => 2]
        ];

        $standardShipping = new StandardShipping();
        $this->info("▶ Using shipping strategy: {$standardShipping->getName()}");
        $this->info("  Estimated delivery: {$standardShipping->getEstimatedDays()} days");

        $order1 = $this->orderService->createOrder($customer1, $items1, $standardShipping);
        $this->orderService->processPayment($order1->id, 'stripe', ['card_number' => '4242424242424242']);
        $this->orderService->shipOrder($order1->id, 'FedEx');

        $this->info("\n═══════════════════════════════════════════════════════\n");
        sleep(2);

        // SCENARIO 2: Express Shipping with PayPal
        $this->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->info("║  SCENARIO 2: Express Order with PayPal                ║");
        $this->info("╚═══════════════════════════════════════════════════════╝\n");

        $items2 = [
            ['product' => $product3, 'quantity' => 1],
            ['product' => $product4, 'quantity' => 1]
        ];

        $expressShipping = new ExpressShipping();
        $this->info("▶ Using shipping strategy: {$expressShipping->getName()}");
        $this->info("  Estimated delivery: {$expressShipping->getEstimatedDays()} days");

        $order2 = $this->orderService->createOrder($customer2, $items2, $expressShipping);
        $this->orderService->processPayment($order2->id, 'paypal', ['email' => 'jane@example.com']);
        $this->orderService->shipOrder($order2->id, 'UPS');

        $this->info("\n═══════════════════════════════════════════════════════\n");
        sleep(2);

        // SCENARIO 3: Overnight Shipping with Crypto
        $this->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->info("║  SCENARIO 3: Overnight Order with Cryptocurrency      ║");
        $this->info("╚═══════════════════════════════════════════════════════╝\n");

        $items3 = [
            ['product' => $product1, 'quantity' => 1],
            ['product' => $product2, 'quantity' => 1],
            ['product' => $product3, 'quantity' => 1],
            ['product' => $product4, 'quantity' => 1]
        ];

        $overnightShipping = new OvernightShipping();
        $this->info("▶ Using shipping strategy: {$overnightShipping->getName()}");
        $this->info("  Estimated delivery: {$overnightShipping->getEstimatedDays()} day");

        $order3 = $this->orderService->createOrder($customer1, $items3, $overnightShipping);
        $this->orderService->processPayment($order3->id, 'crypto', ['wallet' => '0x742d35Cc...']);
        $this->orderService->shipOrder($order3->id, 'DHL');

        $this->info("\n═══════════════════════════════════════════════════════\n");

        // SUMMARY
        $this->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->info("║                  FINAL SUMMARY                        ║");
        $this->info("╚═══════════════════════════════════════════════════════╝\n");

        $this->info("📊 Design Patterns Used:\n");
        $this->info("1️⃣  STRATEGY PATTERN");
        $this->info("   → StandardShipping, ExpressShipping, OvernightShipping");
        $this->info("   → Interchangeable shipping algorithms\n");

        $this->info("2️⃣  FACTORY PATTERN");
        $this->info("   → PaymentFactory creates payment gateways");
        $this->info("   → NotificationFactory creates notification channels\n");

        $this->info("3️⃣  REPOSITORY PATTERN");
        $this->info("   → OrderRepository, ProductRepository, CustomerRepository");
        $this->info("   → Abstracts data access from business logic\n");

        $this->info("4️⃣  OBSERVER PATTERN");
        $this->info("   → Laravel Events: OrderCreated, OrderPaid, OrderShipped");
        $this->info("   → Listeners handle notifications, inventory, analytics\n");

        $this->info("═══════════════════════════════════════════════════════");
        $this->info("\n✨ All patterns working together in harmony! ✨\n");

        return Command::SUCCESS;
    }
}
