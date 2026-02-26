<?php

namespace App\Services\Shipping\Carriers\ValueObjects;

readonly class Address
{
    public function __construct(
        public string $street,
        public string $city,
        public string $state,
        public string $zipCode,
        public string $country = 'US'
    ) {}

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
            'country' => $this->country,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['street'],
            $data['city'],
            $data['state'],
            $data['zipCode'] ?? $data['zip_code'],
            $data['country'] ?? 'US'
        );
    }
}
