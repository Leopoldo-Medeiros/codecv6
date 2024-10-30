<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search'); // It gets the value of the search field, if it exists
        $courses = Course::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                         ->orWhere('description', 'like', '%' . $search . '%');
        })->paginate(10);

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.form');
    }

    public function store(CourseRequest $request)
    {
        $validatedData = $request->validated();

        // Cria o curso com o user_id do usuário autenticado
        Course::create([
            'name' => $validatedData['name'],
            'slug' => $validatedData['slug'],
            'description' => $validatedData['description'],
        ]);

        return redirect()->route('courses.index')->with('success', 'Course created successfully!');
    }


    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('courses.form', compact('course'));
    }

    public function update(CourseRequest $request, Course $course)
    {
        $validatedData = $request->validated();

        $course->update($validatedData);
        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
