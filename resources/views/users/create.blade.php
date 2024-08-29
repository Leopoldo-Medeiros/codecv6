@extends('layouts.admin')

@section('content')
    <h1>{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control mb-3" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control mb-3" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control mb-3" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Create</button>
    </form>

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
