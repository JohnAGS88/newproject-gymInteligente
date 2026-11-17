<?php

namespace App\Enums;

enum QrCheckinStatus: string
{
    case Valid = 'valid';
    case Duplicate = 'duplicate';
    case Expired = 'expired';
    case Rejected = 'rejected';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}