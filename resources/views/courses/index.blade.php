@extends('layouts.admin')
@section('title', 'Courses List')
@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <section>
        <div class="container bootstrap snippets bootdey">
            <div class="px-2">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <h1 class="text-xl fw-bold large-text">Courses List</h1>
                    </div>
                    <div class="col-lg-6 text-end">
                        <a href="{{ route('courses.create') }}" class="btn btn-primary btn-sm mb-4">Create Course</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <!-- Search Bar -->
                                <div class="input-group mb-3">
                                    <input type="text" id="search" placeholder="Search by name or slug" class="form-control" value="{{ request('search') }}">
                                    <button id="searchButton" class="btn btn-primary" type="button">Search</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table course-list mt-3">
                                        <thead>
                                        <tr>
                                            <th><span>Course Name</span></th>
                                            <th><span>Slug</span></th>
                                            <th class="text-center"><span>Action</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($courses as $course)
                                            <tr>
                                                <td>{{ $course->name }}</td>
                                                <td>{{ $course->slug }}</td>
                                                <td class="text-center" style="width: 20%;">
                                                    <a href="{{ route('courses.show', $course->id) }}" class="table-link text-warning" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('courses.edit', $course->id) }}" class="table-link text-info" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="table-link text-danger" style="border: none; background: none;" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination Links -->
                                <div class="d-flex justify-content-center">
                                    {{ $courses->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript for Search Functionality -->
    <script type="text/javascript">
        document.getElementById('searchButton').addEventListener('click', function() {
            const searchValue = document.getElementById('search').value;
            window.location.href = "{{ route('courses.index') }}?search=" + encodeURIComponent(searchValue);
        });

        // Optional: Trigger search on enter key press
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('searchButton').click();
            }
        });
    </script>

@endsection
