@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-box text-purple-600"></i>
            Products
        </h1>
        <p class="text-gray-600 mt-2">Browse all available products</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <div class="bg-gradient-to-br from-purple-100 to-blue-100 p-8 text-center">
                    <i class="fas fa-laptop text-6xl text-purple-600"></i>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $product->name }}</h3>

                    <div class="mb-4">
                        <span class="text-3xl font-bold text-purple-600">${{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-box w-5"></i>
                            <span>Stock: <strong>{{ $product->stock }}</strong></span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-weight w-5"></i>
                            <span>Weight: <strong>{{ $product->weight }} kg</strong></span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('products.show', $product->id) }}" class="flex-1 bg-purple-600 text-white text-center py-2 rounded hover:bg-purple-700 transition">
                            Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($products->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No products available</p>
        </div>
    @endif
@endsection
