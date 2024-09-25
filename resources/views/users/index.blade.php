@extends('layouts.admin')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container bootstrap snippets bootdey">
        <div class="row">
            <div class="col-lg-6">
                <h1 class="text-xl fw-bold large-text">Users List</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm mb-4">Create User</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <!-- Search Bar -->
                        <div class="input-group mb-3">
                            <input type="text" id="search" placeholder="Search by name or email" class="form-control" value="{{ request('search') }}">
                            <button id="searchButton" class="btn btn-primary" type="button">Search</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table user-list mt-3">
                                <thead>
                                <tr>
                                    <th><span>User</span></th>
                                    <th><span>Created</span></th>
                                    <th class="text-center"><span>Email</span></th>
                                    <th class="text-center"><span>Action</span></th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td class="align-middle">
                                            @if ($user->profile->profile_image)
                                                <img src="{{ Storage::url($user->profile->profile_image) }}" alt="Profile Image" class="rounded-circle small-avatar">
                                            @else
                                                <img src="{{ asset('images/team-13.jpg') }}" alt="Default Image" class="rounded-circle small-avatar">
                                            @endif
                                            <a href="{{ route('users.show', $user->id) }}" class="user-link small-user-link">{{ $user->fullname }}</a>
                                            <span class="user-subhead">{{ $user->roles->pluck('name')->map('ucfirst')->join(', ') }}</span>
                                        </td>
                                        <td class="align-middle">{{ $user->created_at->format('Y/m/d') }}</td>
                                        <td class="text-center align-middle">
                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                        </td>
                                        <td class="align-middle" style="width: 20%;">
                                            <a href="{{ route('users.show', $user->id) }}" class="table-link text-warning">
                                                <span class="fa-stack">
                                                    <i class="fa fa-square fa-stack-2x"></i>
                                                    <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="table-link text-info">
                                                <span class="fa-stack">
                                                    <i class="fa fa-square fa-stack-2x"></i>
                                                    <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="table-link danger" style="border: none; background: none;">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-trash fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            <nav aria-label="Page navigation example">
                {!! $users->links() !!}
            </nav>
        </div>
    </div>

    <!-- JavaScript for Search Functionality -->
    <script type="text/javascript">
        document.getElementById('searchButton').addEventListener('click', function() {
            const searchValue = document.getElementById('search').value;
            window.location.href = "{{ route('users.index') }}?search=" + encodeURIComponent(searchValue);
        });

        // Optional: Trigger search on enter key press
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('searchButton').click();
            }
        });
    </script>
@endsection
