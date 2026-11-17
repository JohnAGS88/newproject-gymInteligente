<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;

/**
 * Orquesta cobros de membresías.
 *
 * - Efectivo: registro inmediato como `succeeded` + auditoría inmutable.
 * - Stripe: PaymentIntent tokenizado (modo test con claves vacías => se crea
 *   el pago en `pending` sin llamar a Stripe, listo para el webhook).
 */
class PaymentService
{
    public function recordCashPayment(
        User $causer,
        Membership $membership,
        int $amount,
        string $currency,
        ?string $reference,
        array $metadata,
    ): Payment {
        $payment = Payment::create([
            'user_id' => $membership->user_id,
            'membership_id' => $membership->getKey(),
            'branch_id' => $membership->branch_id,
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'method' => PaymentMethod::Cash->value,
            'status' => PaymentStatus::Succeeded->value,
            'reference' => $reference ?: 'CASH-'.Str::upper(Str::random(10)),
            'paid_at' => now(),
            'metadata' => $metadata,
        ]);

        activity()
            ->performedOn($payment)
            ->causedBy($causer)
            ->withProperties([
                'ip_address' => request()->ip(),
                'before' => null,
                'after' => $payment->toArray(),
            ])
            ->log('Pago en efectivo registrado');

        return $payment;
    }

    /**
     * @return array{payment: Payment, client_secret: string|null, stripe_configured: bool}
     */
    public function createStripePayment(User $causer, Membership $membership, int $amount, string $currency, array $metadata): array
    {
        $currency = strtoupper($currency);

        $payment = Payment::create([
            'user_id' => $membership->user_id,
            'membership_id' => $membership->getKey(),
            'branch_id' => $membership->branch_id,
            'amount' => $amount,
            'currency' => $currency,
            'method' => PaymentMethod::Stripe->value,
            'status' => PaymentStatus::Pending->value,
            'reference' => null,
            'paid_at' => null,
            'metadata' => $metadata,
        ]);

        if (! $this->stripeConfigured()) {
            return [
                'payment' => $payment,
                'client_secret' => null,
                'stripe_configured' => false,
            ];
        }

        $intent = Cashier::stripe()->paymentIntents->create([
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => [
                'payment_id' => $payment->getKey(),
                'membership_id' => $membership->getKey(),
            ],
        ]);

        $intentData = $intent->toArray();

        $payment->forceFill([
            'stripe_payment_intent_id' => (string) ($intentData['id'] ?? ''),
        ])->save();

        return [
            'payment' => $payment->fresh() ?? $payment,
            'client_secret' => isset($intentData['client_secret']) ? (string) $intentData['client_secret'] : null,
            'stripe_configured' => true,
        ];
    }

    public function stripeConfigured(): bool
    {
        return filled(config('cashier.key')) && filled(config('cashier.secret'));
    }
}