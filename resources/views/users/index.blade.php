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

        <table class="table custom-table">
            <thead>
            <tr>
                <th>FULL NAME</th>
                <th>EMAIL</th>
                <th>ROLE</th>
                <th>ACTIONS</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td data-label="FULL NAME">{{ $user->fullname }}</td>
                    <td data-label="EMAIL">{{ $user->email }}</td>
                    <td data-label="ROLE">{{ $user->roles->pluck('name')->map('ucfirst')->join(', ') }}</td>
                    <td data-label="ACTIONS">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
@endsection
