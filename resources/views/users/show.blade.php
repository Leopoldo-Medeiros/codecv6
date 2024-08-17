@extends('layouts.admin')

@section('content')
    <h1>User Details</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $user->name }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="card-text"><strong>Birthdate:</strong> {{ $user->profile->birthdate }}</p>
            <p class="card-text"><strong>Role:</strong> {{ $user->profile->role }}</p>
        </div>
    </div>
@endsection
