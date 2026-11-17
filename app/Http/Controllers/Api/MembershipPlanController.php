<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MembershipPlan\StoreMembershipPlanRequest;
use App\Http\Requests\MembershipPlan\UpdateMembershipPlanRequest;
use App\Models\MembershipPlan;
use Illuminate\Http\JsonResponse;

class MembershipPlanController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', MembershipPlan::class);

        return response()->json(MembershipPlan::orderBy('name')->get());
    }

    public function store(StoreMembershipPlanRequest $request): JsonResponse
    {
        $this->authorize('create', MembershipPlan::class);

        $plan = MembershipPlan::create($request->validated());

        return response()->json($plan, 201);
    }

    public function show(MembershipPlan $membershipPlan): JsonResponse
    {
        $this->authorize('view', $membershipPlan);

        return response()->json($membershipPlan);
    }

    public function update(UpdateMembershipPlanRequest $request, MembershipPlan $membershipPlan): JsonResponse
    {
        $this->authorize('update', $membershipPlan);

        $membershipPlan->update($request->validated());

        return response()->json($membershipPlan);
    }

    public function destroy(MembershipPlan $membershipPlan): JsonResponse
    {
        $this->authorize('delete', $membershipPlan);

        $membershipPlan->delete();

        return response()->json(null, 204);
    }
}