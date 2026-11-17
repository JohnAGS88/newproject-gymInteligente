<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case Active = 'active';
    case Frozen = 'frozen';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}