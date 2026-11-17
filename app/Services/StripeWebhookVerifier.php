<?php

namespace App\Services;

use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Webhook;

/**
 * Verifica la firma criptográfica de los webhooks de Stripe.
 *
 * Se mantiene aislado para poder simularse en tests de idempotencia sin
 * depender de la red de Stripe.
 */
class StripeWebhookVerifier
{
    /**
     * @return array{id: string, type: string, object: array<string, mixed>}
     *
     * @throws SignatureVerificationException
     * @throws UnexpectedValueException
     */
    public function verify(string $payload, string $signatureHeader, string $secret, int $tolerance): array
    {
        $event = Webhook::constructEvent($payload, $signatureHeader, $secret, $tolerance);

        $eventData = $event->toArray();

        /** @var array<string, mixed> $data */
        $data = is_array($eventData['data'] ?? null) ? $eventData['data'] : [];

        /** @var array<string, mixed> $object */
        $object = is_array($data['object'] ?? null) ? $data['object'] : [];

        return [
            'id' => (string) ($eventData['id'] ?? ''),
            'type' => (string) ($eventData['type'] ?? ''),
            'object' => $object,
        ];
    }
}