@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Dashboard</h1> <!-- Removido o text-center para alinhar à esquerda -->
        <div class="row">
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header"><b>Total Users</b></div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalUsers }}</h5>
                        <p class="card-text">Total registered users on the platform.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header"><b>Total Admins</b></div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalAdmins }}</h5>
                        <p class="card-text">Total active admins.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

