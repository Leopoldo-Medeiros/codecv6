@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Users List</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="float-right mb-3">
            <a href="{{ route('users.create') }}" class="btn btn-success">Create User</a>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td><a href="{{ route('users.show', $user->id) }}">{{ $user->fullname }}</a></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roles->pluck('name')->map('ucfirst')->join(', ') }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
