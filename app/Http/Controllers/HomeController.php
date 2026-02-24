<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        return view('home', compact('products', 'totalProducts', 'totalCustomers'));
    }
}
