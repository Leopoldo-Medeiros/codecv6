<?php

namespace App\Services;

use App\Models\Path;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PathService
{
    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Path::query()->with(['consultant', 'plans']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): Path
    {
        return Path::with(['consultant', 'plans'])->findOrFail($id);
    }

    public function create(array $data): Path
    {
        $path = Path::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'consultant_id' => $data['consultant_id'],
        ]);

        if (isset($data['plan_ids'])) {
            $path->plans()->sync($data['plan_ids']);
        }

        return $path->load(['consultant', 'plans']);
    }

    public function update(Path $path, array $data): Path
    {
        $path->update([
            'name' => $data['name'] ?? $path->name,
            'description' => $data['description'] ?? $path->description,
            'consultant_id' => $data['consultant_id'] ?? $path->consultant_id,
        ]);

        if (isset($data['plan_ids'])) {
            $path->plans()->sync($data['plan_ids']);
        }

        return $path->fresh(['consultant', 'plans']);
    }

    public function delete(Path $path): void
    {
        $path->plans()->detach();
        $path->delete();
    }
}
