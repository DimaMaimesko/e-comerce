@extends('layouts.app')

@section('title', 'Home - E-Commerce System')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            <i class="fas fa-home text-purple-600"></i>
            Welcome to E-Commerce System
        </h1>
        <p class="text-gray-600 text-lg">Demonstrating Clean Architecture with 4 Design Patterns</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm uppercase font-semibold">Total Products</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-box text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm uppercase font-semibold">Total Customers</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalCustomers }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-users text-green-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm uppercase font-semibold">In Stock</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $products->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-warehouse text-purple-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Design Patterns Info -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-layer-group text-purple-600 mr-2"></i>
            Design Patterns Implemented
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border-l-4 border-blue-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    <i class="fas fa-random mr-2"></i>1. Strategy Pattern
                </h3>
                <p class="text-gray-600">Interchangeable shipping algorithms (Standard, Express, Overnight) that calculate costs differently.</p>
            </div>

            <div class="border-l-4 border-green-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    <i class="fas fa-industry mr-2"></i>2. Factory Pattern
                </h3>
                <p class="text-gray-600">Payment and Notification factories that create gateway instances (Stripe, PayPal, Crypto).</p>
            </div>

            <div class="border-l-4 border-yellow-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    <i class="fas fa-database mr-2"></i>3. Repository Pattern
                </h3>
                <p class="text-gray-600">Data access abstraction for Orders, Products, and Customers with clean interfaces.</p>
            </div>

            <div class="border-l-4 border-purple-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    <i class="fas fa-bell mr-2"></i>4. Observer Pattern
                </h3>
                <p class="text-gray-600">Event-driven architecture with Laravel Events (OrderCreated, OrderPaid, OrderShipped).</p>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Available Products
            </h2>
            <a href="{{ route('products.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($products->take(4) as $product)
                <div class="border rounded-lg p-4 hover:shadow-lg transition">
                    <div class="bg-gray-100 rounded-lg p-6 mb-4 text-center">
                        <i class="fas fa-laptop text-4xl text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">{{ $product->name }}</h3>
                    <p class="text-2xl font-bold text-purple-600 mb-2">${{ number_format($product->price, 2) }}</p>
                    <p class="text-sm text-gray-600 mb-4">
                        <i class="fas fa-box mr-1"></i>Stock: {{ $product->stock }}
                    </p>
                    <a href="{{ route('products.show', $product->id) }}" class="block text-center bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition">
                        View Details
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('orders.create') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg p-6 hover:shadow-xl transition transform hover:-translate-y-1">
            <i class="fas fa-plus-circle text-3xl mb-2"></i>
            <h3 class="text-xl font-bold">Create New Order</h3>
            <p class="text-sm mt-2 opacity-90">Start a new order with shipping strategies</p>
        </a>

        <a href="{{ route('products.index') }}" class="bg-gradient-to-r from-blue-600 to-teal-600 text-white rounded-lg p-6 hover:shadow-xl transition transform hover:-translate-y-1">
            <i class="fas fa-box text-3xl mb-2"></i>
            <h3 class="text-xl font-bold">Browse Products</h3>
            <p class="text-sm mt-2 opacity-90">View all available products in stock</p>
        </a>

        <a href="{{ route('orders.index') }}" class="bg-gradient-to-r from-teal-600 to-green-600 text-white rounded-lg p-6 hover:shadow-xl transition transform hover:-translate-y-1">
            <i class="fas fa-receipt text-3xl mb-2"></i>
            <h3 class="text-xl font-bold">View Orders</h3>
            <p class="text-sm mt-2 opacity-90">Track all orders and their statuses</p>
        </a>
    </div>
@endsection
