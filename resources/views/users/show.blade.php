<!-- resources/views/users/show.blade.php -->

@extends('layouts.admin')

@section('content')
    <h1>User Details</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">{{ $user->name }}</h5>
            <p class="card-text"><strong>Full name:</strong> {{ $user->fullname }}</p>
            <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="card-text"><strong>Role:</strong> {{ ucfirst($user->getRoleNames()->first()) }}</p>
            <p class="card-text"><strong>Birth date:</strong> {{ $user->profile->birth_date }}</p>
            <p class="card-text"><strong>Profession:</strong> {{ $user->profile->profession }}</p>
        </div>
    </div>
@endsection
