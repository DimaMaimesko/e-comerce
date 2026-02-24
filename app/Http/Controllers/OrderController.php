<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\Shipping\StandardShipping;
use App\Services\Shipping\ExpressShipping;
use App\Services\Shipping\OvernightShipping;
use App\Repositories\Contracts\OrderRepositoryInterface;
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

        $shippingMethods = [
            'standard' => 'Standard Shipping (7 days)',
            'express' => 'Express Shipping (3 days)',
            'overnight' => 'Overnight Shipping (1 day)',
        ];

        return view('orders.create', compact('customers', 'products', 'shippingMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'shipping_method' => 'required|in:standard,express,overnight',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

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
            $shippingStrategy = match($validated['shipping_method']) {
                'standard' => new StandardShipping(),
                'express' => new ExpressShipping(),
                'overnight' => new OvernightShipping(),
            };

            // Create order
            $order = $this->orderService->createOrder($customer, $items, $shippingStrategy);

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
