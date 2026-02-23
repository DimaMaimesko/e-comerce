<?php

namespace App\Repositories;

use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function find(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function save(Customer $customer): Customer
    {
        $customer->save();
        return $customer;
    }

    public function delete(int $id): bool
    {
        return Customer::destroy($id) > 0;
    }
}
