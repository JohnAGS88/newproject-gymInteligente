<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ExerciseGymGifController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\MembershipPlanController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\QrCheckinController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Prefijo global `/api/v1`. Autenticación por token Sanctum. Cada recurso
| está protegido por su Policy (autorización) y su FormRequest (validación).
|
*/

Route::prefix('v1')->group(function (): void {
    // Autenticación pública.
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // Webhook de Stripe (llamado por Stripe, sin auth; firma verificada).
    Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Sucursales.
        Route::apiResource('branches', BranchController::class);

        // Planes de membresía.
        Route::apiResource('membership-plans', MembershipPlanController::class);

        // Membresías (+ congelamiento).
        Route::apiResource('memberships', MembershipController::class);
        Route::post('/memberships/{membership}/freeze', [MembershipController::class, 'freeze']);
        Route::post('/memberships/{membership}/unfreeze', [MembershipController::class, 'unfreeze']);

        // Pagos (inmutables: sin update/delete; el reembolso es explícito y auditado).
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::post('/payments', [PaymentController::class, 'store'])->middleware('throttle:30,1');
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund']);

        // QR dinámico + check-ins.
        Route::post('/qr/issue', [QrCheckinController::class, 'issue'])->middleware('throttle:30,1');
        Route::post('/qr-checkins/validate', [QrCheckinController::class, 'validate'])->middleware('throttle:60,1');
        Route::get('/qr-checkins', [QrCheckinController::class, 'index']);
        Route::get('/qr-checkins/{qrCheckin}', [QrCheckinController::class, 'show']);

        // Catálogo de ejercicios.
        Route::apiResource('exercises', ExerciseGymGifController::class);
    });
});