<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlanService
{
    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Plan::query()->with(['consultant', 'clients']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): Plan
    {
        return Plan::with(['consultant', 'clients', 'paths'])->findOrFail($id);
    }

    public function create(array $data): Plan
    {
        $plan = Plan::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? 0,
            'consultant_id' => $data['consultant_id'],
        ]);

        if (isset($data['client_ids'])) {
            $plan->clients()->sync($data['client_ids']);
        }

        if (isset($data['path_ids'])) {
            $plan->paths()->sync($data['path_ids']);
        }

        return $plan->load(['consultant', 'clients', 'paths']);
    }

    public function update(Plan $plan, array $data): Plan
    {
        $plan->update([
            'name' => $data['name'] ?? $plan->name,
            'description' => $data['description'] ?? $plan->description,
            'price' => $data['price'] ?? $plan->price,
            'consultant_id' => $data['consultant_id'] ?? $plan->consultant_id,
        ]);

        if (isset($data['client_ids'])) {
            $plan->clients()->sync($data['client_ids']);
        }

        if (isset($data['path_ids'])) {
            $plan->paths()->sync($data['path_ids']);
        }

        return $plan->fresh(['consultant', 'clients', 'paths']);
    }

    public function delete(Plan $plan): void
    {
        $plan->clients()->detach();
        $plan->paths()->detach();
        $plan->delete();
    }

    public function attachClient(Plan $plan, int $clientId): void
    {
        $plan->clients()->attach($clientId);
    }

    public function detachClient(Plan $plan, int $clientId): void
    {
        $plan->clients()->detach($clientId);
    }

    public function findOrCreateForClient(int $consultantId, int $clientId): Plan
    {
        $plan = Plan::where('consultant_id', $consultantId)
            ->whereHas('clients', fn ($q) => $q->where('users.id', $clientId))
            ->first();

        if (! $plan) {
            $client = User::findOrFail($clientId);
            $plan = Plan::create([
                'name' => "{$client->fullname}'s Training Plan",
                'consultant_id' => $consultantId,
            ]);
            $plan->clients()->attach($clientId);
        }

        return $plan;
    }

    public function assignPathToClient(int $consultantId, int $clientId, int $pathId): void
    {
        $plan = $this->findOrCreateForClient($consultantId, $clientId);
        $plan->paths()->syncWithoutDetaching([$pathId]);
    }

    public function removePathFromClient(int $consultantId, int $clientId, int $pathId): void
    {
        $plan = Plan::where('consultant_id', $consultantId)
            ->whereHas('clients', fn ($q) => $q->where('users.id', $clientId))
            ->first();

        $plan?->paths()->detach($pathId);
    }
}
