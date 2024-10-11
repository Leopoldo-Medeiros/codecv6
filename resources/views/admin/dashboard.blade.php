@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        @if (Auth::check())
            @role('admin')
            <div class="col-lg-6">
                <h1 class="text-xl fw-bold large-text">Admin Dashboard</h1>
            </div>
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header large-font"><b>Total Users</b></div>
                        <div class="card-body">
                            <h5 class="card-title large-font">{{ $totalUsers }}</h5>
                            <p class="card-text large-font">Total registered users on the platform</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header large-font"><b>Total Admins</b></div>
                        <div class="card-body">
                            <h5 class="card-title large-font">{{ $totalAdmins }}</h5>
                            <p class="card-text large-font">Total active admins</p>
                        </div>
                    </div>
                </div>
            </div>
            @endrole
        @else
            <h2 class="mb-4 fw-bold">Client Dashboard</h2>
            <div class="col-md-12">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header large-font"><b>Client Dashboard</b></div>
                    <div class="card-body">
                        @if (Auth::check() && Auth::user()->name)
                            <h5 class="card-title large-font">Welcome, {{ Auth::user()->name }}!</h5>
                        @else
                            <h5 class="card-title large-font">Welcome, Guest</h5>
                        @endif
                        <p class="card-text large-font">This is your client dashboard</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
