<!-- resources/views/users/index.blade.php -->
@extends('layouts.admin')

@section('content')
    <h1>Users</h1>
    @if($users->isEmpty())
        <p>No users found.</p>
    @else
        <table class="table mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Birthdate</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->profile->birth_date ?? 'N/A' }}</td>
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
