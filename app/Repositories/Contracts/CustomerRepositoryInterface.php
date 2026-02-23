<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function find(int $id): ?Customer;
    public function findByEmail(string $email): ?Customer;
    public function save(Customer $customer): Customer;
    public function delete(int $id): bool;
}
