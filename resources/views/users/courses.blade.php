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

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="text-xl fw-bold large-text">Courses List</h1>
                </div>
                <div class="col-lg-6 text-end">
                    <a href="{{ route('courses.create') }}" class="btn btn-primary btn-sm mb-4">Create Course</a>
                </div>
            </div>

            <div class="card shadow mb-4">
                 <div class="card-body">
                     <!-- Search Bar -->
                     <div class="input-group mb-3">
                         <input type="text" id="search" placeholder="Search by name" class="form-control" value="{{ request('search') }}">
                         <button id="searchButton" class="btn btn-primary" type="button">Search</button>
                     </div>

                     <!-- Courses Table -->
                     <div class="table-responsive">
                         <table class="table">
                             <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>Name</th>
                                     <th>Description</th>
                                     <th>User</th>
                                     <th>Actions</th>
                                 </tr>
                             </thead>
                             <tbody>
                                @foreach($courses as $course)
                                    <tr>
                                        <td>{{ $course->id }}</td>
                                        <td>{{ $course->name }}</td>
                                        <td>{{ $course->slug }}</td>
                                        <td>{{ $course->description }}</td>
                                        <td>{{ $course->user->fullname }}</td>
                                        <td>
                                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('course.destroy', $course->id) }}" method="POST" class="d-inline">
                                                @csrf@method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                             </tbody>
                         </table>
                     </div>

                     <!-- Pagination -->
                     <div class="d-flex justify-content-center">
                         {{ $courses->links('vendor.pagination.bootstrap-5') }}
                     </div>
                 </div>
            </div>
        </div>
    </section>

    <!-- Search Function -->
    <script type="text/javascript">
        document.getElementById('searchButton').addEventListener('click', function() {
            const searchValue = document.getElementById('search').value;
            window.location.href = "{{ route('courses.index') }}?search=" + encodeURIComponent(searchValue);
        });
    </script>
@endsection
