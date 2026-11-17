<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Stripe = 'stripe';
    case Cash = 'cash';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}