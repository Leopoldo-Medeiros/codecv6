<!-- resources/views/profile.blade.php -->
@extends('layouts.admin')

@section('content')
    <section style="background-color: #eee;">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
                            <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            @if ($user->profile && $user->profile->profile_image)
                                <img src="{{ Storage::url($user->profile->profile_image) }}" alt="avatar" class="rounded-circle img-fluid profile-image">
                            @else
                                <img src="{{ asset('images/team-13.jpg') }}" alt="avatar" class="rounded-circle img-fluid profile-image">
                            @endif
                            <p class="text-muted mb-1">{{ $user->profile->profession ?? '' }}</p>
                            <p class="text-muted mb-4">{{ $user->profile->location ?? '' }}</p>
                            <div class="d-flex justify-content-center mb-2">
                                <button type="button" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-1"></i> Message
                                </button>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning ms-2">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 mb-lg-0">
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush rounded-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fab fa-github fa-lg text-body"></i>
                                    @if($user->profile && $user->profile->github)
                                        <a href="{{ $user->profile->github }}" target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; display: block;">{{ $user->profile->github }}</a>
                                    @else
                                        <p class="mb-0">
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-muted small">
                                                <i class="fas fa-plus-circle me-1"></i>Add your GitHub
                                            </a>
                                        </p>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fab fa-linkedin fa-lg" style="color: #0077b5;"></i>
                                    @if($user->profile && isset($user->profile->linkedin) && $user->profile->linkedin)
                                        <a href="{{ $user->profile->linkedin }}" target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; display: block;">{{ $user->profile->linkedin }}</a>
                                    @else
                                        <p class="mb-0">
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-muted small">
                                                <i class="fas fa-plus-circle me-1"></i>Add your LinkedIn
                                            </a>
                                        </p>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                                    @if($user->profile && $user->profile->instagram)
                                        <a href="{{ $user->profile->instagram }}" target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; display: block;">{{ $user->profile->instagram }}</a>
                                    @else
                                        <p class="mb-0">
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-muted small">
                                                <i class="fas fa-plus-circle me-1"></i>Add your Instagram
                                            </a>
                                        </p>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                                    @if($user->profile && $user->profile->facebook)
                                        <a href="{{ $user->profile->facebook }}" target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; display: block;">{{ $user->profile->facebook }}</a>
                                    @else
                                        <p class="mb-0">
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-muted small">
                                                <i class="fas fa-plus-circle me-1"></i>Add your Facebook
                                            </a>
                                        </p>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-sm fw-bold mt-4">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold"><i class="fas fa-user me-2"></i>Full Name</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->fullname }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold"><i class="fas fa-envelope me-2"></i>Email</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->email }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold"><i class="fas fa-birthday-cake me-2"></i>Birth Date</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->profile->birth_date ?? '' }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 fw-bold"><i class="fas fa-briefcase me-2"></i>Profession</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->profile->profession ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4 mb-md-0">
                                <div class="card-body">
                                    <p class="mb-4"><span class="text-primary font-italic me-1">assignment</span> Project Status</p>
                                    <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                                    <div class="progress rounded mb-2" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4 mb-md-0">
                                <div class="card-body">
                                    <p class="mb-4"><span class="text-primary font-italic me-1">assignment</span> Project Status</p>
                                    <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                                    <div class="progress rounded" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                                    <div class="progress rounded mb-2" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
