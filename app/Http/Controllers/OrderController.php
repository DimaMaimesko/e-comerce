<?php

namespace App\Http\Controllers;

use App\Enums\ShippingMethodEnum;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function index()
    {
        $orders = Order::with('customer')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found');
        }

        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('stock', '>', 0)->get();

        $shippingMethods = ShippingMethodEnum::options();

        return view('orders.create', compact('customers', 'products', 'shippingMethods'));
    }

    public function store(OrderStoreRequest $request)
    {
        $validated = $request->validated();
        try {
            $customer = Customer::findOrFail($validated['customer_id']);

            // Prepare items with product objects
            $items = [];
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity']
                ];
            }

            // Get shipping strategy
            $shippingStrategy = $request->getShippingStrategy();

            // Apply pricing modifiers based on promo code
            $modifiers = [];
            if ($validated['promo_code'] ?? null) {
                $modifiers = match(strtoupper($validated['promo_code'])) {
                    'BLACKFRIDAY' => [
                        ['type' => 'discount', 'percentage' => 25],
                        ['type' => 'bulk', 'min_quantity' => 10, 'discount' => 15],
                        ['type' => 'tax', 'rate' => 0.10],
                    ],
                    'WINTER' => [
                        ['type' => 'seasonal', 'season' => 'Winter', 'multiplier' => 0.90], // 10% winter discount
                    ],
                    default => [],
                };
            }

            // Create order
            $order = $this->orderService->createOrder($customer, $items, $shippingStrategy, $modifiers);

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function showPayment(int $id)
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found');
        }

        if ($order->isPaid()) {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Order is already paid');
        }

        $paymentMethods = [
            'stripe' => 'Credit Card (Stripe)',
            'paypal' => 'PayPal',
            'crypto' => 'Cryptocurrency',
        ];

        return view('orders.payment', compact('order', 'paymentMethods'));
    }

    public function processPayment(Request $request, int $id)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:stripe,paypal,crypto',
        ]);

        try {
            $paymentDetails = []; // In real app, collect card/account details

            $this->orderService->processPayment(
                $id,
                $validated['payment_method'],
                $paymentDetails
            );

            return redirect()->route('orders.show', $id)
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function showShipping(int $id)
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found');
        }

        if (!$order->isPaid()) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Order must be paid before shipping');
        }

        if ($order->status === Order::STATUS_SHIPPED) {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Order is already shipped');
        }

        $carriers = ['FedEx', 'UPS', 'DHL', 'USPS'];

        return view('orders.shipping', compact('order', 'carriers'));
    }

    public function processShipping(Request $request, int $id)
    {
        $validated = $request->validate([
            'carrier' => 'required|string|max:50',
        ]);

        try {
            $this->orderService->shipOrder($id, $validated['carrier']);

            return redirect()->route('orders.show', $id)
                ->with('success', 'Order shipped successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Shipping failed: ' . $e->getMessage());
        }
    }
}
