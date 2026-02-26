<?php

namespace Tests\Unit;

use App\Exceptions\ValidationException;
use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderValidation\StockValidator;
use App\Services\OrderValidation\CustomerCreditValidator;
use App\Services\OrderValidation\MinimumOrderValueValidator;
use App\Services\OrderValidation\PaymentMethodValidator;
use Tests\TestCase;

class OrderValidationChainTest extends TestCase
{
    public function test_stock_validator_passes_with_sufficient_stock()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 50]);

        $orderData = [
            'customer' => $customer,
            'items' => [
                ['product' => $product, 'quantity' => 5]
            ],
            'payment_method' => 'credit_card'
        ];

        $validator = new StockValidator();

        // Should not throw exception
        $validator->validate($orderData);

        $this->assertTrue(true); // If we reach here, validation passed
    }

    public function test_stock_validator_fails_with_insufficient_stock()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('insufficient stock');

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 3, 'price' => 50]);

        $orderData = [
            'customer' => $customer,
            'items' => [
                ['product' => $product, 'quantity' => 10]
            ]
        ];

        $validator = new StockValidator();
        $validator->validate($orderData);
    }

    public function test_customer_credit_validator_passes_within_limit()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'outstanding_balance' => 500
        ]);

        $orderData = [
            'customer' => $customer,
            'items' => []
        ];

        $validator = new CustomerCreditValidator();
        $validator->validate($orderData);

        $this->assertTrue(true);
    }

    public function test_customer_credit_validator_fails_over_limit()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('exceeded credit limit');

        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'outstanding_balance' => 1500
        ]);

        $orderData = [
            'customer' => $customer,
            'items' => []
        ];

        $validator = new CustomerCreditValidator();
        $validator->validate($orderData);
    }

    public function test_minimum_order_value_validator_passes()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 50]);

        $orderData = [
            'customer' => $customer,
            'items' => [
                ['product' => $product, 'quantity' => 1]
            ]
        ];

        $validator = new MinimumOrderValueValidator(10.0);
        $validator->validate($orderData);

        $this->assertTrue(true);
    }

    public function test_minimum_order_value_validator_fails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('must be at least');

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 5]);

        $orderData = [
            'customer' => $customer,
            'items' => [
                ['product' => $product, 'quantity' => 1]
            ]
        ];

        $validator = new MinimumOrderValueValidator(10.0);
        $validator->validate($orderData);
    }

    public function test_payment_method_validator_passes_with_valid_card()
    {
        $customer = Customer::factory()->create([
            'has_valid_credit_card' => true
        ]);

        $orderData = [
            'customer' => $customer,
            'items' => [],
            'payment_method' => 'credit_card'
        ];

        $validator = new PaymentMethodValidator();
        $validator->validate($orderData);

        $this->assertTrue(true);
    }

    public function test_payment_method_validator_fails_without_card()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('no valid credit card');

        $customer = Customer::factory()->create([
            'has_valid_credit_card' => false
        ]);

        $orderData = [
            'customer' => $customer,
            'items' => [],
            'payment_method' => 'credit_card'
        ];

        $validator = new PaymentMethodValidator();
        $validator->validate($orderData);
    }

    public function test_full_validation_chain_passes()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'outstanding_balance' => 100,
            'has_valid_credit_card' => true
        ]);

        $product = Product::factory()->create([
            'stock' => 100,
            'price' => 50
        ]);

        $orderData = [
            'customer' => $customer,
            'items' => [
                ['product' => $product, 'quantity' => 2]
            ],
            'payment_method' => 'credit_card'
        ];

        // Build the validation chain
        $stockValidator = new StockValidator();
        $creditValidator = new CustomerCreditValidator();
        $minimumOrderValidator = new MinimumOrderValueValidator(10.0);
        $paymentValidator = new PaymentMethodValidator();

        $stockValidator
            ->setNext($creditValidator)
            ->setNext($minimumOrderValidator)
            ->setNext($paymentValidator);

        // Should pass all validators
        $stockValidator->validate($orderData);

        $this->assertTrue(true);
    }

    public function test_validation_chain_stops_at_first_failure()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('insufficient stock');

        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'outstanding_balance' => 1500, // This would fail too, but stock fails first
            'has_valid_credit_card' => true
        ]);

        $product = Product::factory()->create([
            'stock' => 1,
            'price' => 50
        ]);

        $orderData = [
            'customer' => $customer,
            'items' => [
                ['product' => $product, 'quantity' => 10] // This will fail first
            ],
            'payment_method' => 'credit_card'
        ];

        // Build the chain
        $stockValidator = new StockValidator();
        $creditValidator = new CustomerCreditValidator();

        $stockValidator->setNext($creditValidator);

        // Should stop at stock validator
        $stockValidator->validate($orderData);
    }
}
