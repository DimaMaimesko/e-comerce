<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Commerce System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
<!-- Navigation -->
<nav class="gradient-bg text-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold">
                    <i class="fas fa-shopping-cart mr-2"></i>E-Commerce
                </a>
            </div>

            <div class="hidden md:flex space-x-6">
                <a href="{{ route('home') }}" class="hover:text-gray-200 transition {{ request()->routeIs('home') ? 'border-b-2' : '' }}">
                    <i class="fas fa-home mr-1"></i>Home
                </a>
                <a href="{{ route('products.index') }}" class="hover:text-gray-200 transition {{ request()->routeIs('products.*') ? 'border-b-2' : '' }}">
                    <i class="fas fa-box mr-1"></i>Products
                </a>
                <a href="{{ route('customers.index') }}" class="hover:text-gray-200 transition {{ request()->routeIs('customers.*') ? 'border-b-2' : '' }}">
                    <i class="fas fa-users mr-1"></i>Customers
                </a>
                <a href="{{ route('orders.index') }}" class="hover:text-gray-200 transition {{ request()->routeIs('orders.*') ? 'border-b-2' : '' }}">
                    <i class="fas fa-receipt mr-1"></i>Orders
                </a>
            </div>

            <a href="{{ route('orders.create') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                <i class="fas fa-plus mr-1"></i>New Order
            </a>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
    <div class="container mx-auto px-4 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="container mx-auto px-4 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="container mx-auto px-4 mt-4">
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('info') }}</span>
        </div>
    </div>
@endif

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-gray-800 text-white mt-12">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <p>&copy; 2024 E-Commerce System. Demonstrating 4 Design Patterns.</p>
            <div class="mt-4 md:mt-0">
                    <span class="text-sm">
                        <i class="fas fa-layer-group mr-1"></i>Strategy • Factory • Repository • Observer
                    </span>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
