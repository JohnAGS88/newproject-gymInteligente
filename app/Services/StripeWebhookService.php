<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Scopes\BranchScope;
use App\Models\StripeWebhookEvent;
use Illuminate\Database\QueryException;

/**
 * Procesa eventos de Stripe con idempotencia por `event_id`.
 *
 * El registro `stripe_webhook_events` (event_id único) garantiza que un mismo
 * evento jamás se procese dos veces, incluso ante reintentos o carreras.
 */
class StripeWebhookService
{
    /**
     * @param  array{id: string, type: string, object: array<string, mixed>}  $event
     * @return array{processed: bool, event: StripeWebhookEvent}
     */
    public function handle(array $event): array
    {
        $existing = StripeWebhookEvent::where('event_id', $event['id'])->first();

        if ($existing !== null) {
            return ['processed' => false, 'event' => $existing];
        }

        try {
            $record = StripeWebhookEvent::create([
                'event_id' => $event['id'],
                'type' => $event['type'],
                'payload' => $event,
            ]);
        } catch (QueryException) {
            /** @var StripeWebhookEvent $winner */
            $winner = StripeWebhookEvent::where('event_id', $event['id'])->firstOrFail();

            return ['processed' => false, 'event' => $winner];
        }

        $this->process($event);

        $record->forceFill(['processed_at' => now()])->save();

        return ['processed' => true, 'event' => $record];
    }

    /**
     * @param  array{id: string, type: string, object: array<string, mixed>}  $event
     */
    private function process(array $event): void
    {
        match ($event['type']) {
            'payment_intent.succeeded' => $this->markPayment($event, PaymentStatus::Succeeded),
            'payment_intent.payment_failed' => $this->markPayment($event, PaymentStatus::Failed),
            'charge.refunded' => $this->markPayment($event, PaymentStatus::Refunded),
            default => null,
        };
    }

    /**
     * @param  array{id: string, type: string, object: array<string, mixed>}  $event
     */
    private function markPayment(array $event, PaymentStatus $status): void
    {
        $intentId = (string) ($event['object']['id'] ?? '');

        if ($intentId === '') {
            return;
        }

        $payment = Payment::withoutGlobalScope(BranchScope::class)
            ->where('stripe_payment_intent_id', $intentId)
            ->first();

        if ($payment === null) {
            return;
        }

        $before = $payment->toArray();

        $payment->forceFill([
            'status' => $status->value,
            'paid_at' => $status === PaymentStatus::Succeeded ? now() : $payment->paid_at,
        ])->save();

        activity()
            ->performedOn($payment)
            ->withProperties([
                'ip_address' => request()->ip(),
                'stripe_event_id' => $event['id'],
                'before' => $before,
                'after' => $payment->toArray(),
            ])
            ->log('Estado de pago actualizado por webhook de Stripe');
    }
}