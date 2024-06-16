{{-- resources/views/users/courses.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>Courses</h1>
    @if($courses)
        @foreach($courses as $course)
            <div>
                <h2>{{ $course->name }}</h2>
                <p>{{ $course->description }}</p>
            </div>
        @endforeach
    @else
        <p>No courses found</p>
    @endif
@endsection
