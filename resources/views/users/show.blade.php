@extends('layouts.admin')

@section('content')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <h3 class="fs-4 text-secondary mb-3 text-uppercase text-center fw-bold">User Details</h3>
                <hr class="w-40 mx-auto mb-4 border-dark-subtle">
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card" style="width: 24rem;">
                    <img src="https://mdbootstrap.com/img/new/avatars/8.jpg" class="card-img-top" alt="team-member-foto">
                    <div class="card-body">
                        <h5 class="card-title">{{ $user->name }}</h5>
                        <p class="card-text"><strong>Role:</strong> {{ ucfirst($user->getRoleNames()->first()) }}</p>
                        <p class="card-text"><strong>Full name:</strong> {{ $user->fullname }}</p>
                        <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
                        <p class="card-text"><strong>Birth date:</strong> {{ $user->profile->birth_date }}</p>
                        <p class="card-text"><strong>Profession:</strong> {{ $user->profile->profession }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <a href="javascript:history.back()" class="btn btn-primary btn-sm fw-bold mt-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
@endsection
