<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\RefundPaymentRequest;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);

        /** @var User $user */
        $user = $request->user();

        $query = Payment::with(['membership', 'branch']);

        if (! $user->can('view-payments')) {
            $query->where('user_id', $user->getKey());
        }

        return response()->json($query->latest()->get());
    }

    public function store(StorePaymentRequest $request, PaymentService $payments): JsonResponse
    {
        $this->authorize('create', Payment::class);

        /** @var User $user */
        $user = $request->user();

        $membership = Membership::findOrFail((string) $request->validated('membership_id'));

        $amount = (int) $request->validated('amount');
        $currency = (string) $request->validated('currency');
        $method = (string) $request->validated('method');
        $reference = $request->validated('reference') !== null ? (string) $request->validated('reference') : null;

        /** @var array<string, mixed> $metadata */
        $metadata = $request->validated('metadata') ?? [];

        if ($method === PaymentMethod::Cash->value) {
            $payment = $payments->recordCashPayment($user, $membership, $amount, $currency, $reference, $metadata);

            return response()->json(['payment' => $payment], 201);
        }

        $result = $payments->createStripePayment($user, $membership, $amount, $currency, $metadata);

        return response()->json([
            'payment' => $result['payment'],
            'client_secret' => $result['client_secret'],
            'stripe_configured' => $result['stripe_configured'],
        ], 201);
    }

    public function show(Payment $payment): JsonResponse
    {
        $this->authorize('view', $payment);

        return response()->json($payment->load(['membership', 'branch']));
    }

    public function refund(RefundPaymentRequest $request, Payment $payment): JsonResponse
    {
        $this->authorize('refund', $payment);

        if ($payment->status !== PaymentStatus::Succeeded) {
            return response()->json(['message' => 'Solo se pueden reembolsar pagos exitosos.'], 422);
        }

        $before = $payment->toArray();

        $payment->forceFill(['status' => PaymentStatus::Refunded->value])->save();

        activity()
            ->performedOn($payment)
            ->causedBy($request->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'reason' => $request->validated('reason'),
                'before' => $before,
                'after' => $payment->toArray(),
            ])
            ->log('Pago reembolsado');

        return response()->json($payment);
    }
}