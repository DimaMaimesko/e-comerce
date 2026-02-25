<?php

namespace App\Http\Requests\Order;

use App\Enums\ShippingMethodEnum;
use App\Services\Shipping\ExpressShipping;
use App\Services\Shipping\OvernightShipping;
use App\Services\Shipping\ShippingStrategy;
use App\Services\Shipping\StandardShipping;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OrderStoreRequest extends FormRequest
{
    private ?ShippingStrategy $shippingStrategy = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'shipping_method' => ['required', new Enum(ShippingMethodEnum::class)],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'promo_code' => 'nullable|string',
        ];
    }

    public function getShippingStrategy(): ShippingStrategy
    {
        if ($this->shippingStrategy === null) {
            $this->shippingStrategy = match($this->validated('shipping_method')) {
                'standard' => new StandardShipping(),
                'express' => new ExpressShipping(),
                'overnight' => new OvernightShipping(),
            };

        }
        if ($this->shippingStrategy === null) {
            $method = ShippingMethodEnum::from($this->validated('shipping_method'));
            $strategyClass = $method->getStrategyClass();
            $this->shippingStrategy = new $strategyClass();
        }

        return $this->shippingStrategy;
    }
}
