<?php

namespace App\Enums;

enum QrCheckinType: string
{
    case Qr = 'qr';
    case FaceId = 'face_id';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}