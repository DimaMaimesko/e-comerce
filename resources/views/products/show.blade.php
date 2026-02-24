@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('products.index') }}" class="text-purple-600 hover:text-purple-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/2 bg-gradient-to-br from-purple-100 to-blue-100 p-12 flex items-center justify-center">
                    <i class="fas fa-laptop text-9xl text-purple-600"></i>
                </div>

                <div class="md:w-1/2 p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>

                    <div class="mb-6">
                        <span class="text-4xl font-bold text-purple-600">${{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="flex items-center justify-between py-3 border-b">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-box mr-3 text-purple-600"></i>
                            Stock Available
                        </span>
                            <span class="font-semibold text-gray-800">{{ $product->stock }} units</span>
                        </div>

                        <div class="flex items-center justify-between py-3 border-b">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-weight mr-3 text-purple-600"></i>
                            Weight
                        </span>
                            <span class="font-semibold text-gray-800">{{ $product->weight }} kg</span>
                        </div>

                        <div class="flex items-center justify-between py-3 border-b">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-hashtag mr-3 text-purple-600"></i>
                            Product ID
                        </span>
                            <span class="font-semibold text-gray-800">#{{ $product->id }}</span>
                        </div>

                        <div class="flex items-center justify-between py-3">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-chart-line mr-3 text-purple-600"></i>
                            Status
                        </span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>
                        </div>
                    </div>

                    @if($product->stock > 0)
                        <a href="{{ route('orders.create') }}" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                            <i class="fas fa-shopping-cart mr-2"></i>Order Now
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-300 text-gray-500 text-center py-3 rounded-lg cursor-not-allowed font-semibold">
                            <i class="fas fa-ban mr-2"></i>Out of Stock
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
