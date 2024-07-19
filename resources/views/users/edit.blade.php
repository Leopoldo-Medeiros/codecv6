@extends('layouts.admin')

@section('content')
    <h1>Edit user</h1>
    <form action="{{ route('users.update') }}" method="PUT">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="name">Email</label>
            <input type="email" class="form-control" id="email" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="name">Password</label>
            <input type="password" class="form-control" id="password" value="{{ $user->password }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
