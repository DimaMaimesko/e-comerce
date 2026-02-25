@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('orders.index') }}" class="text-purple-600 hover:text-purple-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>

        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-plus-circle text-purple-600"></i>
                Create New Order
            </h1>

            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                @csrf

                <!-- Customer Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i>Select Customer
                    </label>
                    <select name="customer_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Choose a customer...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shipping Method (Strategy Pattern) -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-truck mr-1"></i>Shipping Method (Strategy Pattern)
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($shippingMethods as $key => $label)
                            <label class="relative">
                                <input type="radio" name="shipping_method" value="{{ $key }}"
                                       {{ old('shipping_method') == $key ? 'checked' : '' }}
                                       class="peer sr-only" required>
                                <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-purple-500 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition">
                                    <div class="font-semibold text-gray-800">
                                        @if($key === 'standard')
                                            <i class="fas fa-box text-blue-600"></i>
                                        @elseif($key === 'express')
                                            <i class="fas fa-shipping-fast text-green-600"></i>
                                        @else
                                            <i class="fas fa-rocket text-purple-600"></i>
                                        @endif
                                        {{ ucfirst($key) }}
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $label }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('shipping_method')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Products Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-shopping-cart mr-1"></i>Select Products
                    </label>

                    <div id="productItems" class="space-y-4">
                        <!-- Product items will be added here -->
                    </div>

                    <button type="button" onclick="addProductRow()" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </button>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Total Items:</span>
                            <span id="totalItems" class="font-semibold">0</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Shipping cost and totals will be calculated after order creation
                        </div>
                    </div>
                </div>

                <!-- Promo Code -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1"></i>Promo Code (Optional)
                    </label>
                    <input type="text" name="promo_code" value="{{ old('promo_code') }}"
                           placeholder="Enter BLACKFRIDAY or WINTER"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent uppercase">
                    <p class="text-xs text-gray-500 mt-1">Try: BLACKFRIDAY (25% off + Bulk discount + Tax) or WINTER (10% seasonal discount)</p>
                    @error('promo_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-check mr-2"></i>Create Order
                    </button>
                    <a href="{{ route('orders.index') }}" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-400 transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const products = @json($products);
            let productIndex = 0;

            function addProductRow() {
                const container = document.getElementById('productItems');
                const row = document.createElement('div');
                row.className = 'flex gap-4 items-start border-b pb-4';
                row.id = `productRow${productIndex}`;

                row.innerHTML = `
            <div class="flex-1">
                <select name="items[${productIndex}][product_id]" required
                        onchange="updateSummary()"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="">Select product...</option>
                    ${products.map(p => `
                        <option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">
                            ${p.name} - $${parseFloat(p.price).toFixed(2)} (Stock: ${p.stock})
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="items[${productIndex}][quantity]"
                       placeholder="Qty" min="1" required
                       onchange="updateSummary()"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            <button type="button" onclick="removeProductRow(${productIndex})"
                    class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600">
                <i class="fas fa-trash"></i>
            </button>
        `;

                container.appendChild(row);
                productIndex++;
                updateSummary();
            }

            function removeProductRow(index) {
                const row = document.getElementById(`productRow${index}`);
                if (row) {
                    row.remove();
                    updateSummary();
                }
            }

            function updateSummary() {
                const rows = document.querySelectorAll('#productItems > div');
                let totalItems = 0;

                rows.forEach(row => {
                    const quantityInput = row.querySelector('input[type="number"]');
                    if (quantityInput && quantityInput.value) {
                        totalItems += parseInt(quantityInput.value);
                    }
                });

                document.getElementById('totalItems').textContent = totalItems;
            }

            // Add first product row on page load
            document.addEventListener('DOMContentLoaded', function() {
                addProductRow();
            });
        </script>
    @endpush
@endsection
