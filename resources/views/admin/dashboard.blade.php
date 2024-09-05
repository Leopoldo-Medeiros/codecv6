@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Admin Dashboard</h1>
        <div class="row">
            @role('admin')
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header"><b>Total Users</b></div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalUsers }}</h5>
                        <p class="card-text">Total registered users on the platform</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header"><b>Total Admins</b></div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalAdmins }}</h5>
                        <p class="card-text">Total active admins</p>
                    </div>
                </div>
            </div>
            @else
                <div class="col-md-12">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header"><b>Client Dashboard</b></div>
                        <div class="card-body">
                            <h5 class="card-title">Welcome, {{ Auth::user()->name }}</h5>
                            <p class="card-text">This is your client dashboard</p>
                        </div>
                    </div>
                </div>
                @endrole
        </div>
    </div>
@endsection
