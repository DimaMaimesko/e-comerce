<?php

namespace Tests\Unit;

use App\Services\Factories\PaymentFactory;
use App\Services\Payment\StripeGateway;
use App\Services\Payment\PayPalGateway;
use App\Services\Payment\CryptoGateway;
use Tests\TestCase;

class PaymentFactoryTest extends TestCase
{
    private PaymentFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payment.stripe.api_key' => 'test_key',
            'payment.paypal.client_id' => 'test_id',
            'payment.paypal.client_secret' => 'test_secret',
            'payment.crypto.wallet_address' => 'test_address',
        ]);

        $this->factory = new PaymentFactory();
    }

    public function test_it_creates_stripe_gateway()
    {
        $gateway = $this->factory->createPaymentGateway('stripe');

        $this->assertInstanceOf(StripeGateway::class, $gateway);
        $this->assertEquals('Stripe', $gateway->getName());
    }

    public function test_it_creates_paypal_gateway()
    {
        $gateway = $this->factory->createPaymentGateway('paypal');

        $this->assertInstanceOf(PayPalGateway::class, $gateway);
        $this->assertEquals('PayPal', $gateway->getName());
    }

    public function test_it_creates_crypto_gateway()
    {
        $gateway = $this->factory->createPaymentGateway('crypto');

        $this->assertInstanceOf(CryptoGateway::class, $gateway);
        $this->assertEquals('Cryptocurrency', $gateway->getName());
    }

    public function test_it_throws_exception_for_unsupported_gateway()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createPaymentGateway('invalid');
    }
}
