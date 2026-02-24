@extends('layouts.app')

@section('title', $customer->name)

@section('content')
    <div class="max-w-6xl mx-auto">
        <a href="{{ route('customers.index') }}" class="text-purple-600 hover:text-purple-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-20 w-20 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-purple-600"></i>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-800">{{ $customer->name }}</h1>
                        <p class="text-gray-500 mt-1">Customer ID: #{{ $customer->id }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Contact Information</h3>
                    <div class="space-y-2">
                        <p class="flex items-center text-gray-700">
                            <i class="fas fa-envelope w-6 text-purple-600"></i>
                            {{ $customer->email }}
                        </p>
                        <p class="flex items-center text-gray-700">
                            <i class="fas fa-phone w-6 text-purple-600"></i>
                            {{ $customer->phone }}
                        </p>
                    </div>
                </div>

                @if($customer->addresses && count($customer->addresses) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Addresses</h3>
                        <div class="space-y-2">
                            @foreach($customer->addresses as $address)
                                <p class="flex items-start text-gray-700">
                                    <i class="fas fa-map-marker-alt w-6 text-purple-600 mt-1"></i>
                                    {{ $address }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Orders -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-receipt text-purple-600 mr-2"></i>
                Order History ({{ $orders->count() }})
            </h2>

            @if($orders->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No orders yet</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="border rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-800">Order #{{ $order->id }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $order->created_at->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-purple-600">
                                        ${{ number_format($order->getGrandTotal(), 2) }}
                                    </p>
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-2
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('orders.show', $order->id) }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
