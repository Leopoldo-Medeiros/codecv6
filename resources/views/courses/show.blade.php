@extends('layouts.admin')
@section('title', 'Course Details')
@section('content')

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="text-xl fw-bold">Course Details</h1>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <p><strong>ID:</strong> {{ $course->id }}</p>
                            <p><strong>Name:</strong> {{ $course->name }}</p>
                            <p><strong>Slug (URL):</strong> {{ $course->slug }}</p>
                            <p><strong>Description:</strong> {{ $course->description }}</p>
                            <p><strong>User:</strong> {{ $course->user->fullname }}</p>
                            <p><strong>Created at:</strong> {{ $course->created_at }}</p>
                            <p><strong>Updated at:</strong> {{ $course->updated_at }}</p>

                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning">Edit</a>
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
