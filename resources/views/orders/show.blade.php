@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
    <div class="max-w-6xl mx-auto">
        <a href="{{ route('orders.index') }}" class="text-purple-600 hover:text-purple-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Order #{{ $order->id }}</h1>
                    <p class="text-gray-500 mt-1">Created {{ $order->created_at->format('M d, Y g:i A') }}</p>
                </div>
                <div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    <i class="fas fa-circle text-xs mr-1"></i>{{ ucfirst($order->status) }}
                </span>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="border-t pt-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Customer Information</h3>
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('customers.show', $order->customer->id) }}" class="text-lg font-semibold text-purple-600 hover:text-purple-800">
                            {{ $order->customer->name }}
                        </a>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-envelope mr-1"></i>{{ $order->customer->email }}
                            <span class="mx-2">•</span>
                            <i class="fas fa-phone mr-1"></i>{{ $order->customer->phone }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-box text-purple-600"></i>
                        Order Items
                    </h2>

                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between border-b pb-4">
                                <div class="flex items-center flex-1">
                                    <div class="flex-shrink-0 h-16 w-16 bg-gray-100 rounded flex items-center justify-center">
                                        <i class="fas fa-laptop text-2xl text-purple-600"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h3 class="font-semibold text-gray-800">{{ $item['name'] }}</h3>
                                        <p class="text-sm text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                                        @if(isset($item['price_description']))
                                            <p class="text-xs text-purple-600 italic">
                                                <i class="fas fa-tag mr-1 text-[10px]"></i>{{ $item['price_description'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">${{ number_format($item['price'], 2) }}</p>
                                    <p class="text-sm text-gray-500">× {{ $item['quantity'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Info -->
                @if($order->tracking_number)
                    <div class="bg-white rounded-lg shadow-md p-8 mt-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-shipping-fast text-purple-600"></i>
                            Shipping Information
                        </h2>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Tracking Number</p>
                                    <p class="text-xl font-bold text-purple-600">{{ $order->tracking_number }}</p>
                                </div>
                                <i class="fas fa-truck text-4xl text-purple-400"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h3>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span class="font-semibold">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Shipping ({{ $order->shipping_method ?? 'N/A' }}):</span>
                            <span class="font-semibold">${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-lg font-bold text-gray-900">
                            <span>Total:</span>
                            <span class="text-purple-600">${{ number_format($order->getGrandTotal(), 2) }}</span>
                        </div>
                    </div>

                    @if($order->payment_transaction_id)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-green-700 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Payment Confirmed
                            </p>
                            <p class="text-xs text-green-600 mt-1">
                                Transaction: {{ $order->payment_transaction_id }}
                            </p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        @if($order->status === 'pending')
                            <a href="{{ route('orders.payment', $order->id) }}"
                               class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                                <i class="fas fa-credit-card mr-2"></i>Process Payment
                            </a>
                        @endif

                        @if($order->status === 'paid')
                            <a href="{{ route('orders.shipping', $order->id) }}"
                               class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                                <i class="fas fa-shipping-fast mr-2"></i>Ship Order
                            </a>
                        @endif

                        @if($order->status === 'shipped' || $order->status === 'delivered')
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                                <i class="fas fa-check-circle text-purple-600 text-2xl mb-2"></i>
                                <p class="text-sm text-purple-700 font-semibold">Order {{ ucfirst($order->status) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
