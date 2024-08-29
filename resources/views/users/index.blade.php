<!-- resources/views/users/index.blade.php -->
@extends('layouts.admin')

@section('content')
    <h1>Users</h1>
    <a href="{{ route('users.create') }}" class="btn btn-success mb-3 float-right">Create User</a>
    @if($users->isEmpty())
        <p>No users found.</p>
    @else
        <table class="table mt-3">
            <thead>
            <tr>
                <th><i class="fas fa-id-badge"></i> ID</th>
                <th><i class="fas fa-user"></i> Name</th>
                <th><i class="fas fa-user-tag"></i> Role</th>
                <th><i class="fas fa-envelope"></i> Email</th>
                <th><i class="fas fa-cogs"></i> Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary-custom">Edit</a>
                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
