<?php

namespace App\Http\Controllers\Api;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Membership\FreezeMembershipRequest;
use App\Http\Requests\Membership\StoreMembershipRequest;
use App\Http\Requests\Membership\UnfreezeMembershipRequest;
use App\Http\Requests\Membership\UpdateMembershipRequest;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Membership::class);

        /** @var User $user */
        $user = $request->user();

        $query = Membership::with(['user', 'branch', 'plan']);

        if (! $user->can('view-memberships')) {
            // Un socio solo ve sus propias membresías.
            $query->where('user_id', $user->getKey());
        }

        return response()->json($query->orderByDesc('starts_at')->get());
    }

    public function store(StoreMembershipRequest $request): JsonResponse
    {
        $this->authorize('create', Membership::class);

        $membership = Membership::create($request->validated());

        return response()->json($membership->load(['user', 'branch', 'plan']), 201);
    }

    public function show(Membership $membership): JsonResponse
    {
        $this->authorize('view', $membership);

        return response()->json($membership->load(['user', 'branch', 'plan']));
    }

    public function update(UpdateMembershipRequest $request, Membership $membership): JsonResponse
    {
        $this->authorize('update', $membership);

        $membership->update($request->validated());

        return response()->json($membership->load(['user', 'branch', 'plan']));
    }

    public function destroy(Request $request, Membership $membership): JsonResponse
    {
        $this->authorize('delete', $membership);

        $before = $membership->toArray();

        $membership->forceFill(['status' => MembershipStatus::Cancelled->value])->save();

        activity()
            ->performedOn($membership)
            ->causedBy($request->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'before' => $before,
                'after' => $membership->toArray(),
            ])
            ->log('Membresía cancelada');

        return response()->json($membership);
    }

    public function freeze(FreezeMembershipRequest $request, Membership $membership): JsonResponse
    {
        $this->authorize('freeze', $membership);

        $before = $membership->toArray();

        $frozenUntil = $request->validated('frozen_until')
            ? now()->parse($request->validated('frozen_until'))
            : now()->addDays(30);

        $membership->forceFill([
            'status' => MembershipStatus::Frozen->value,
            'frozen_until' => $frozenUntil,
        ])->save();

        activity()
            ->performedOn($membership)
            ->causedBy($request->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'before' => $before,
                'after' => $membership->toArray(),
            ])
            ->log('Membresía congelada');

        return response()->json($membership);
    }

    public function unfreeze(UnfreezeMembershipRequest $request, Membership $membership): JsonResponse
    {
        $this->authorize('unfreeze', $membership);

        $before = $membership->toArray();

        $membership->forceFill([
            'status' => MembershipStatus::Active->value,
            'frozen_until' => null,
        ])->save();

        activity()
            ->performedOn($membership)
            ->causedBy($request->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'before' => $before,
                'after' => $membership->toArray(),
            ])
            ->log('Membresía descongelada');

        return response()->json($membership);
    }
}