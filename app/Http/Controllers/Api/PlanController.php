<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanService $planService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $plans = $this->planService->paginate(
            $request->input('search'),
            $request->input('per_page', 10)
        );

        return PlanResource::collection($plans);
    }

    public function show(Plan $plan): PlanResource
    {
        $plan = $this->planService->find($plan->id);

        return new PlanResource($plan);
    }

    public function store(PlanRequest $request): JsonResponse
    {
        $plan = $this->planService->create($request->validated());

        return response()->json([
            'message' => 'Plan created successfully',
            'plan' => new PlanResource($plan),
        ], 201);
    }

    public function update(PlanRequest $request, Plan $plan): JsonResponse
    {
        $plan = $this->planService->update($plan, $request->validated());

        return response()->json([
            'message' => 'Plan updated successfully',
            'plan' => new PlanResource($plan),
        ]);
    }

    public function destroy(Plan $plan): JsonResponse
    {
        $this->planService->delete($plan);

        return response()->json(['message' => 'Plan deleted successfully']);
    }

    public function attachClient(Request $request, Plan $plan): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
        ]);

        $this->planService->attachClient($plan, $validated['client_id']);

        return response()->json(['message' => 'Client attached successfully']);
    }

    public function detachClient(Plan $plan, int $userId): JsonResponse
    {
        $this->planService->detachClient($plan, $userId);

        return response()->json(['message' => 'Client detached successfully']);
    }
}
