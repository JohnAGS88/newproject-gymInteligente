<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Token QR criptográficamente inválido o con estructura malformada.
 */
class QrTokenException extends RuntimeException
{
    public const INVALID = 'invalid';

    public function __construct(string $reason = self::INVALID)
    {
        parent::__construct('Token QR inválido: '.$reason);
    }
}