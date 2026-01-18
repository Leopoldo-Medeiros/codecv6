<?php

namespace App\Services;

use App\Models\Plan;
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
            'price' => $data['price'] ?? null,
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
}
