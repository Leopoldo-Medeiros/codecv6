@extends('layouts.admin')
@section('title', isset($course) ? 'Edit Course': 'Create Course')
@section('content')

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="text-xl fw-bold">{{ isset($course) ? 'Edit' : 'Create' }} Course</h1>

                    <form action="{{ isset($course) ? route('courses.update', $course->id) : route('courses.store') }}" method="POST">
                        @csrf
                        @if(isset($course))
                            @method('PUT')
                        @endif

                        <!-- Course Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Course Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $course->name ?? '') }}" required>
                        </div>

                        <!-- Slug (URL) -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Course URL (Slug)</label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $course->slug ?? '') }}" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $course->description ?? '') }}" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success text-bg-primary">{{ isset($course) ? 'Update' : 'Create' }} Course</button>
                        <a href="{{ route('courses.index') }}" class="btn btn-secondary text-white">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
