@extends('layouts.app')

@section('title', 'Process Payment - Order #' . $order->id)

@section('content')
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('orders.show', $order->id) }}" class="text-purple-600 hover:text-purple-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Order
        </a>

        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-credit-card text-purple-600"></i>
                Process Payment
            </h1>
            <p class="text-gray-600 mb-8">Order #{{ $order->id }}</p>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-700">
                        <span>Subtotal:</span>
                        <span class="font-semibold">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>Shipping:</span>
                        <span class="font-semibold">${{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-xl font-bold text-gray-900">
                        <span>Total to Pay:</span>
                        <span class="text-purple-600">${{ number_format($order->getGrandTotal(), 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Method Selection (Factory Pattern) -->
            <form action="{{ route('orders.payment.process', $order->id) }}" method="POST">
                @csrf

                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        <i class="fas fa-wallet mr-1"></i>Select Payment Method (Factory Pattern)
                    </label>

                    <div class="space-y-4">
                        @foreach($paymentMethods as $key => $label)
                            <label class="relative">
                                <input type="radio" name="payment_method" value="{{ $key }}"
                                       class="peer sr-only" required>
                                <div class="border-2 border-gray-300 rounded-lg p-6 cursor-pointer hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            @if($key === 'stripe')
                                                <div class="bg-blue-100 p-3 rounded-full mr-4">
                                                    <i class="fas fa-credit-card text-2xl text-blue-600"></i>
                                                </div>
                                            @elseif($key === 'paypal')
                                                <div class="bg-yellow-100 p-3 rounded-full mr-4">
                                                    <i class="fab fa-paypal text-2xl text-yellow-600"></i>
                                                </div>
                                            @else
                                                <div class="bg-orange-100 p-3 rounded-full mr-4">
                                                    <i class="fab fa-bitcoin text-2xl text-orange-600"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-800 text-lg">{{ $label }}</div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    @if($key === 'stripe')
                                                        Fast and secure card payment
                                                    @elseif($key === 'paypal')
                                                        Pay with your PayPal account
                                                    @else
                                                        Pay with Bitcoin or other crypto
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="hidden peer-checked:block">
                                            <i class="fas fa-check-circle text-2xl text-purple-600"></i>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm text-blue-800 font-semibold mb-1">Design Pattern Demo</p>
                            <p class="text-sm text-blue-700">
                                This payment system demonstrates the <strong>Factory Pattern</strong>.
                                The PaymentFactory creates different payment gateway instances (Stripe, PayPal, Crypto)
                                based on your selection, encapsulating object creation logic.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-4 rounded-lg hover:bg-purple-700 transition font-semibold text-lg">
                        <i class="fas fa-lock mr-2"></i>Pay ${{ number_format($order->getGrandTotal(), 2) }}
                    </button>
                    <a href="{{ route('orders.show', $order->id) }}" class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-lg hover:bg-gray-400 transition font-semibold text-lg text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Payment Gateway Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-white rounded-lg shadow-md p-4 text-center">
                <i class="fas fa-shield-alt text-3xl text-green-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-700">Secure Payment</p>
                <p class="text-xs text-gray-500 mt-1">256-bit SSL Encryption</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 text-center">
                <i class="fas fa-bolt text-3xl text-yellow-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-700">Instant Processing</p>
                <p class="text-xs text-gray-500 mt-1">Real-time confirmation</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 text-center">
                <i class="fas fa-undo text-3xl text-blue-600 mb-2"></i>
                <p class="text-sm font-semibold text-gray-700">Easy Refunds</p>
                <p class="text-xs text-gray-500 mt-1">Hassle-free returns</p>
            </div>
        </div>
    </div>
@endsection
