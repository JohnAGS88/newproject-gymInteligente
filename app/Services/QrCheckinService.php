<?php

namespace App\Services;

use App\Enums\MembershipStatus;
use App\Enums\QrCheckinStatus;
use App\Enums\QrCheckinType;
use App\Exceptions\QrTokenException;
use App\Models\Membership;
use App\Models\QrCheckin;
use App\Models\Scopes\BranchScope;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;

/**
 * Valida un token QR y registra el resultado del check-in en `qr_checkins`.
 *
 * Estados posibles: valid, duplicate, expired, rejected.
 * El anti-replay se apoya en el índice único `token_jti` (a prueba de
 * condiciones de carrera entre dos peticiones simultáneas).
 */
class QrCheckinService
{
    public function __construct(private readonly DynamicQrService $qr)
    {
    }

    /**
     * @return array{http: int, status: string, checkin: QrCheckin|null, message: string}
     */
    public function record(
        string $token,
        string $branchId,
        string $type,
        ?string $presentedDeviceId,
        ?string $ipAddress,
        ?CarbonImmutable $now = null,
    ): array {
        $now ??= CarbonImmutable::now();
        $jti = $this->qr->jti($token);

        if ($this->alreadyUsed($jti)) {
            return $this->result(409, QrCheckinStatus::Duplicate, null, 'El token QR ya fue utilizado.');
        }

        try {
            $payload = $this->qr->verify($token, $now);
        } catch (QrTokenException) {
            return $this->result(422, QrCheckinStatus::Rejected, null, 'Token QR inválido.');
        }

        $checkinType = QrCheckinType::tryFrom($type) ?? QrCheckinType::Qr;

        // Expiración (ventana de 60 segundos).
        if ($now->getTimestamp() > $payload['expires_at']) {
            $checkin = $this->insert($payload['uid'], $payload['device_id'], $jti, $branchId, $checkinType, QrCheckinStatus::Expired, null, $ipAddress);

            return $this->result(422, QrCheckinStatus::Expired, $checkin, 'El token QR expiró.');
        }

        // El token está ligado al device_id del socio: si se presenta desde otro
        // dispositivo, se rechaza (anti-clonación).
        if ($presentedDeviceId !== null && ! hash_equals($payload['device_id'], $presentedDeviceId)) {
            $checkin = $this->insert($payload['uid'], $payload['device_id'], $jti, $branchId, $checkinType, QrCheckinStatus::Rejected, null, $ipAddress);

            return $this->result(422, QrCheckinStatus::Rejected, $checkin, 'El dispositivo no coincide con el token.');
        }

        // El socio debe tener una membresía activa en la sucursal escaneada.
        if (! $this->hasActiveMembership($payload['uid'], $branchId, $now)) {
            $checkin = $this->insert($payload['uid'], $payload['device_id'], $jti, $branchId, $checkinType, QrCheckinStatus::Rejected, null, $ipAddress);

            return $this->result(422, QrCheckinStatus::Rejected, $checkin, 'El socio no tiene una membresía activa en esta sucursal.');
        }

        try {
            $checkin = $this->insert($payload['uid'], $payload['device_id'], $jti, $branchId, $checkinType, QrCheckinStatus::Valid, $now, $ipAddress);
        } catch (QueryException) {
            // Carrera: otra petición consumió el token entre el chequeo previo y
            // este INSERT. El índice único `token_jti` garantiza un solo uso.
            return $this->result(409, QrCheckinStatus::Duplicate, null, 'El token QR ya fue utilizado.');
        }

        return $this->result(201, QrCheckinStatus::Valid, $checkin, 'Check-in registrado.');
    }

    private function alreadyUsed(string $jti): bool
    {
        return QrCheckin::withoutGlobalScope(BranchScope::class)
            ->where('token_jti', $jti)
            ->exists();
    }

    private function hasActiveMembership(string $userId, string $branchId, CarbonImmutable $now): bool
    {
        return Membership::withoutGlobalScope(BranchScope::class)
            ->where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->where('status', MembershipStatus::Active->value)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->where(static function (Builder $query) use ($now): void {
                $query->whereNull('frozen_until')->orWhere('frozen_until', '<=', $now);
            })
            ->exists();
    }

    private function insert(
        string $userId,
        string $deviceId,
        string $jti,
        string $branchId,
        QrCheckinType $type,
        QrCheckinStatus $status,
        ?CarbonImmutable $checkedInAt,
        ?string $ipAddress,
    ): QrCheckin {
        $checkin = new QrCheckin;
        $checkin->user_id = $userId;
        $checkin->branch_id = $branchId;
        $checkin->type = $type;
        $checkin->device_id = $deviceId;
        $checkin->token_jti = $jti;
        $checkin->checked_in_at = $checkedInAt;
        $checkin->status = $status;
        $checkin->ip_address = $ipAddress;
        $checkin->save();

        return $checkin;
    }

    /**
     * @return array{http: int, status: string, checkin: QrCheckin|null, message: string}
     */
    private function result(int $http, QrCheckinStatus $status, ?QrCheckin $checkin, string $message): array
    {
        return [
            'http' => $http,
            'status' => $status->value,
            'checkin' => $checkin,
            'message' => $message,
        ];
    }
}