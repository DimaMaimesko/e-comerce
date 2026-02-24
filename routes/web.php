<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Customers
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

// Orders
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

// Payment
Route::get('/orders/{id}/payment', [OrderController::class, 'showPayment'])->name('orders.payment');
Route::post('/orders/{id}/payment', [OrderController::class, 'processPayment'])->name('orders.payment.process');

// Shipping
Route::get('/orders/{id}/shipping', [OrderController::class, 'showShipping'])->name('orders.shipping');
Route::post('/orders/{id}/shipping', [OrderController::class, 'processShipping'])->name('orders.shipping.process');
