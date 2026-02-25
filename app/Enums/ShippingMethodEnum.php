<?php

namespace App\Enums;

enum ShippingMethodEnum: string
{
    case STANDARD = 'standard';
    case EXPRESS = 'express';
    case OVERNIGHT = 'overnight';

    public function label(): string
    {
        return match($this) {
            self::STANDARD => 'Standard Shipping (7 days)',
            self::EXPRESS => 'Express Shipping (3 days)',
            self::OVERNIGHT => 'Overnight Shipping (1 day)',
        };
    }

    public function getStrategyClass(): string
    {
        return match($this) {
            self::STANDARD => \App\Services\Shipping\StandardShipping::class,
            self::EXPRESS => \App\Services\Shipping\ExpressShipping::class,
            self::OVERNIGHT => \App\Services\Shipping\OvernightShipping::class,
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', array_column(self::cases(), 'value'));
    }
}
