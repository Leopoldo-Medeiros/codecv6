@extends('layouts.admin')

@section('content')
    <h1>{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
    <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="birth_date">Birthdate</label>
            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $user->profile->birth_date ?? '') }}">
        </div>
        <div class="form-group">
            <label for="profession">Profession</label>
            <input type="text" name="profession" class="form-control" value="{{ old('profession', $user->profile->profession ?? '') }}">
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role', isset($user) && $user->roles->contains($role->id) ? $role->id : '') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }}</button>
    </form>
@endsection
