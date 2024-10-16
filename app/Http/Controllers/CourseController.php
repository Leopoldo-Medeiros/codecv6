<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all(); // It gets all courses//
        return response()->json($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request): JsonResponse
    {
        $course = Course::create($request->validated());
        return response()->json($course, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $course = Course::findOrFail($id); // It finds the course or fail if it doesn't exist
        return response()->json($course);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request, string $id): JsonResponse
    {
        $course = Course::findOrFail($id); // It finds the course or fail if it doesn't exist
        $course->update($request->validated()); // Updates the course with validated data
        return response()->json($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $course = Course::findOrFail($id); // Finds the course or fails if it doesn't exist
        $course->delete(); // Deletes the course
        return response()->json(null, 204); // Returns response without content
    }
}
