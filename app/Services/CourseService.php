<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseService
{
    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Course::query()->with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): Course
    {
        return Course::with('user')->findOrFail($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);

        return $course->fresh('user');
    }

    public function delete(Course $course): void
    {
        $course->delete();
    }
}
