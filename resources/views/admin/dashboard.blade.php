{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @role('admin')
                <!-- Admin session -->
                @endrole
                @role('client')
                <!-- Client session -->
                @endrole
            </div>
        </div>
        <div class="col-md-9">
            <h1>Dashboard</h1>
        </div>
    </div>
@endsection
