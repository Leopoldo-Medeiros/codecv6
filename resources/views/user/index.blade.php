@extends('layouts.app')

@section('content')
    <h1>Users</h1>

    @foreach ($users as $user)
        <p>This is user {{ $user->id }}</p>
    @endforeach
@endsection
