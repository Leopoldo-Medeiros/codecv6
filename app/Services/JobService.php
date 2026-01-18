<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobService
{
    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Job::query()->with('consultant');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): Job
    {
        return Job::with('consultant')->findOrFail($id);
    }

    public function create(array $data): Job
    {
        return Job::create($data);
    }

    public function update(Job $job, array $data): Job
    {
        $job->update($data);

        return $job->fresh('consultant');
    }

    public function delete(Job $job): void
    {
        $job->delete();
    }
}
