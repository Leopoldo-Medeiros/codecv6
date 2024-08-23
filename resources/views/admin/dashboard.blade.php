<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p>Welcome to the admin dashboard</p>

    @if(isset($users) && $users->isEmpty())
        <p>No users found.</p>
    @elseif(isset($users))
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
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-danger" data-user-id="{{ $user->id }}" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $user->id }}').submit();">
                            Delete
                        </a>
                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:none;">
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

@section('scripts')
    <script>
        function confirmDelete(event, userId, userName) {
            const loggedInUserId = {{ Auth::id() }};
            if (userId === loggedInUserId) {
                alert('You cannot delete yourself.');
                return false;
            }
            return confirm(`Are you sure you want to delete ${userName}?`);
        }
    </script>
@endsection
