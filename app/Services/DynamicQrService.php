<?php

namespace App\Services;

use App\Exceptions\QrTokenException;
use App\Models\User;
use Carbon\CarbonImmutable;
use JsonException;

/**
 * QR dinámico anti-clonación.
 *
 * Emite un token HMAC (header.payload.signature) que rota cada 60 segundos y
 * queda ligado al `device_id` del socio. El `jti` (hash SHA-256 del token) se
 * usa como clave de un solo uso en `qr_checkins` para detectar reutilización.
 */
class DynamicQrService
{
    public const TOKEN_TTL_SECONDS = 60;

    private const CLOCK_SKEW_SECONDS = 5;

    public function __construct(private readonly string $secret)
    {
    }

    public static function withAppKey(): self
    {
        return new self((string) config('app.key'));
    }

    /**
     * @return array{token: string, expires_in: int, issued_at: string}
     */
    public function issue(User $user, string $deviceId, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();

        $payload = [
            'uid' => $user->getKey(),
            'did' => $deviceId,
            'iat' => $now->getTimestamp(),
            'exp' => $now->getTimestamp() + self::TOKEN_TTL_SECONDS,
        ];

        $json = json_encode($payload, JSON_THROW_ON_ERROR);
        $body = $this->base64UrlEncode((string) $json);
        $signature = hash_hmac('sha256', $body, $this->secret);

        return [
            'token' => $body.'.'.$signature,
            'expires_in' => self::TOKEN_TTL_SECONDS,
            'issued_at' => $now->toIso8601String(),
        ];
    }

    /**
     * Verifica firma y estructura. La expiración temporal se evalúa en el
     * llamador comparando `expires_at` con la hora actual.
     *
     * @return array{uid: string, device_id: string, issued_at: int, expires_at: int}
     *
     * @throws QrTokenException si la firma o la estructura no son válidas.
     */
    public function verify(string $token, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $nowTimestamp = $now->getTimestamp();

        $parts = explode('.', $token);

        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            throw new QrTokenException;
        }

        [$body, $signature] = $parts;

        $expected = hash_hmac('sha256', $body, $this->secret);

        if (! hash_equals($expected, $signature)) {
            throw new QrTokenException;
        }

        $rawPayload = $this->base64UrlDecode($body);

        if ($rawPayload === '') {
            throw new QrTokenException;
        }

        try {
            $payload = json_decode($rawPayload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new QrTokenException;
        }

        if (! is_array($payload)) {
            throw new QrTokenException;
        }

        $uid = $payload['uid'] ?? null;
        $deviceId = $payload['did'] ?? null;
        $issuedAt = $payload['iat'] ?? null;
        $expiresAt = $payload['exp'] ?? null;

        if (! is_string($uid) || ! is_string($deviceId) || ! is_int($issuedAt) || ! is_int($expiresAt)) {
            throw new QrTokenException;
        }

        if ($issuedAt > $expiresAt || $nowTimestamp < $issuedAt - self::CLOCK_SKEW_SECONDS) {
            throw new QrTokenException;
        }

        return [
            'uid' => $uid,
            'device_id' => $deviceId,
            'issued_at' => $issuedAt,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Identificador de un solo uso del token (anti-replay).
     */
    public function jti(string $token): string
    {
        return hash('sha256', $token);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $padded = str_pad(strtr($value, '-_', '+/'), strlen($value) % 4, '=');
        $decoded = base64_decode($padded, true);

        return $decoded === false ? '' : $decoded;
    }
}