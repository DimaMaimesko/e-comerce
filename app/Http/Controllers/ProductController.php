<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function index()
    {
        $products = $this->productRepository->findAll();
        return view('products.index', compact('products'));
    }

    public function show(int $id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return redirect()->route('products.index')
                ->with('error', 'Product not found');
        }

        return view('products.show', compact('product'));
    }
}
