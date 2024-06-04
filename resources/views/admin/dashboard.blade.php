{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @role('admin')
                <!-- Menu Admin -->
                <ul class="list-group">
                    <li class="list-group-item"><a href="{{ route('dashboard') }}">Area Admin</a></li>
                    <li class="list-group-item"><a href="{{ route('logout') }}">Logout</a></li>
                </ul>
                @endrole
                @role('client')
                    <!-- Menu Client -->
                    <ul class="list-group">
                        <li class="list-group-item"><a href="{{ route('client') }}">Area Client</a></li>
                        <li class="list-group-item"><a href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                @endrole
            </div>
            <div class="col-md-9">
                <h1>Dashboard</h1>
            </div>
        </div>
    </div>
@endsection
