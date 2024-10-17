<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Cria o curso com o user_id do usuário autenticado
        Course::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'user_id' => auth()->id(), // Define o user_id como o id do usuário autenticado
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

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:courses,slug,' . $course->id,
            'description' => 'required',
        ]);

        $course->update($request->all());
        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
