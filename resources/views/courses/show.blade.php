@extends('layouts.admin')
@section('title', 'Course Details')
@section('content')

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $course->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100 course-card">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                                <i class="fas fa-book fa-3x"></i>
                            </div>
                            <h3 class="card-title">{{ $course->name }}</h3>
                            <p class="text-muted mb-4">{{ $course->slug }}</p>
                            <div class="d-flex justify-content-center mb-2">
                                <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4 course-card">
                        <div class="card-header bg-white py-3 course-header">
                            <h4 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i>Course Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">ID</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $course->id }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">Name</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $course->name }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">Description</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $course->description }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">Instructor</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $course->user->fullname }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm border-0 course-card">
                        <div class="card-header bg-white py-3 course-header">
                            <h4 class="mb-0 fw-bold"><i class="fas fa-clock me-2"></i>Timeline</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">Created</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $course->created_at->format('F d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold">Last Updated</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $course->updated_at->format('F d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
