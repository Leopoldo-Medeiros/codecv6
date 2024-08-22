<!-- resources/views/users/index.blade.php -->
@extends('layouts.admin')

@section('content')
    <h1>Users</h1>
    @if($users->isEmpty())
        <p>No users found.</p>
    @else
        <table class="table">
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
                        <a href="{{ route('users.destroy', $user) }}" class="btn btn-danger-custom" data-user-id="{{ $user->id }}" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $user->id }}').submit();">
                            Delete
                        </a>
                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
