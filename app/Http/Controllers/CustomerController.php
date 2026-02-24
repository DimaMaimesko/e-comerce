<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function index()
    {
        $customers = Customer::with('orders')->get();
        return view('customers.index', compact('customers'));
    }

    public function show(int $id)
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            return redirect()->route('customers.index')
                ->with('error', 'Customer not found');
        }

        $orders = $customer->orders()->latest()->get();

        return view('customers.show', compact('customer', 'orders'));
    }
}
