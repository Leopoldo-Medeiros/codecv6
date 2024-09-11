@extends('layouts.admin')

@section('content')
    <div class="container bootstrap snippets bootdey">
        <div class="row">
            <div class="col-lg-6">
                <h1 class="text-xl fw-bold large-text">Users List</h1>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('users.create') }}" class="btn btn-primary fw-bold btn-lg">Create User</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box no-header clearfix">
                    <div class="main-box-body clearfix">
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
                                            <img src="https://mdbootstrap.com/img/new/avatars/8.jpg" alt="" class="rounded-circle">
                                            <a href="#" class="user-link">{{ $user->fullname }}</a>
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
                                                            <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                        </span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-center">
                            {{ $users->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
