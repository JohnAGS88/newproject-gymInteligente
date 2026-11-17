<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QrCheckin\IssueQrRequest;
use App\Http\Requests\QrCheckin\ValidateQrCheckinRequest;
use App\Models\QrCheckin;
use App\Models\User;
use App\Services\DynamicQrService;
use App\Services\QrCheckinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrCheckinController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', QrCheckin::class);

        return response()->json(QrCheckin::with(['user', 'branch'])->latest('checked_in_at')->get());
    }

    public function show(QrCheckin $qrCheckin): JsonResponse
    {
        $this->authorize('view', $qrCheckin);

        return response()->json($qrCheckin->load(['user', 'branch']));
    }

    /**
     * Emite un token QR de un solo uso (60 s) ligado al device_id del socio.
     */
    public function issue(IssueQrRequest $request, DynamicQrService $qr): JsonResponse
    {
        $this->authorize('issue', QrCheckin::class);

        /** @var User $user */
        $user = $request->user();

        return response()->json($qr->issue($user, (string) $request->validated('device_id')));
    }

    /**
     * Valida un token QR en el kiosco y registra el check-in.
     */
    public function validate(ValidateQrCheckinRequest $request, QrCheckinService $checkins): JsonResponse
    {
        $this->authorize('validate', QrCheckin::class);

        $deviceId = $request->validated('device_id') !== null ? (string) $request->validated('device_id') : null;

        $result = $checkins->record(
            (string) $request->validated('token'),
            (string) $request->validated('branch_id'),
            (string) $request->validated('type'),
            $deviceId,
            $request->ip(),
        );

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'checkin' => $result['checkin'],
        ], $result['http']);
    }
}