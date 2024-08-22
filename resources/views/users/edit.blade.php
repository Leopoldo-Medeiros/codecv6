<!-- resources/views/users/edit.blade.php -->

@extends('layouts.admin')

@section('content')
    <h1>Edit User</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control mb-2" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control mb-2" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control mb-2">
        </div>

        <div class="form-group">
            <label for="birth_date">Birth Date</label>
            <input type="date" name="birth_date" id="birth_date" class="form-control mb-2" value="{{ old('birth_date', $user->profile->birth_date ?? '') }}">
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
@endsection
