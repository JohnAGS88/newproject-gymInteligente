<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeWebhookService;
use App\Services\StripeWebhookVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, StripeWebhookVerifier $verifier, StripeWebhookService $service): JsonResponse
    {
        $payload = $request->getContent();

        if ($payload === '') {
            return response()->json(['error' => 'Payload vacío.'], 400);
        }

        $secret = (string) config('cashier.webhook.secret');

        if ($secret === '') {
            return response()->json(['error' => 'Webhook de Stripe no configurado.'], 400);
        }

        $signature = (string) $request->header('Stripe-Signature', '');
        $tolerance = (int) config('cashier.webhook.tolerance', 30);

        try {
            $event = $verifier->verify($payload, $signature, $secret, $tolerance);
        } catch (SignatureVerificationException | UnexpectedValueException) {
            return response()->json(['error' => 'Firma de webhook inválida.'], 400);
        }

        $result = $service->handle($event);

        return response()->json([
            'processed' => $result['processed'],
            'event_id' => $event['id'],
        ]);
    }
}