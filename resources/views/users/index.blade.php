@extends('layouts.app')

@section('content')
    <h1>Users</h1>
    <ul>
        @foreach ($users as $user)
            <li><a href="users/{{ $user->id }}">Edit</a> {{ $user->id }} -> {{ $user->name }}</li>
        @endforeach
    </ul>
@endsection
