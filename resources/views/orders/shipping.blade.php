@extends('layouts.app')

@section('title', 'Ship Order - Order #' . $order->id)

@section('content')
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('orders.show', $order->id) }}" class="text-purple-600 hover:text-purple-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Order
        </a>

        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-shipping-fast text-purple-600"></i>
                Ship Order
            </h1>
            <p class="text-gray-600 mb-8">Order #{{ $order->id }}</p>

            <!-- Order Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Customer</p>
                        <p class="font-semibold text-gray-800">{{ $order->customer->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Shipping Method</p>
                        <p class="font-semibold text-gray-800">{{ $order->shipping_method ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Items</p>
                        <p class="font-semibold text-gray-800">{{ count($order->items) }} items</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Weight</p>
                        <p class="font-semibold text-gray-800">{{ $order->getTotalWeight() }} kg</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Form -->
            <form action="{{ route('orders.shipping.process', $order->id) }}" method="POST">
                @csrf

                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        <i class="fas fa-truck mr-1"></i>Select Carrier
                    </label>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach($carriers as $carrier)
                            <label class="relative">
                                <input type="radio" name="carrier" value="{{ $carrier }}"
                                       class="peer sr-only" required>
                                <div class="border-2 border-gray-300 rounded-lg p-6 cursor-pointer hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-purple-100 p-3 rounded-full mr-3">
                                                <i class="fas fa-truck text-xl text-purple-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ $carrier }}</div>
                                                <div class="text-xs text-gray-500 mt-1">Express delivery</div>
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
                    @error('carrier')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm text-blue-800 font-semibold mb-1">Automated Process</p>
                            <p class="text-sm text-blue-700">
                                When you ship this order, the system will automatically:
                            </p>
                            <ul class="text-sm text-blue-700 mt-2 space-y-1 ml-4">
                                <li><i class="fas fa-check mr-2"></i>Generate a tracking number</li>
                                <li><i class="fas fa-check mr-2"></i>Update order status to "Shipped"</li>
                                <li><i class="fas fa-check mr-2"></i>Send email notification to customer (Observer Pattern)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-4 rounded-lg hover:bg-purple-700 transition font-semibold text-lg">
                        <i class="fas fa-paper-plane mr-2"></i>Ship Order
                    </button>
                    <a href="{{ route('orders.show', $order->id) }}" class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-lg hover:bg-gray-400 transition font-semibold text-lg text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
