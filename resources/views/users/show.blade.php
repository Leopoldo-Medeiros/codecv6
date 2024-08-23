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
            <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="card-text"><strong>Birthdate:</strong> {{ $user->profile->birthdate }}</p>
            <!-- Aqui basicamente estou pegando o primeiro nome da role do usuário -->
            <p class="card-text"><strong>Role:</strong> {{ $user->getRoleNames()->first() }}</p>
        </div>
    </div>
@endsection
