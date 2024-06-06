{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @if(Auth()->user()->hasRole('admin'))
                    <!-- Menu Admin -->
                    <ul class="list-group">
                        <li class="list-group-item"><a href="{{ route('users.index') }}">Clients</a></li>
                        <li class="list-group-item"><a href="#">Courses</a></li>
                        <li class="list-group-item"><a href="#">Paths</a></li>
                        <li class="list-group-item"><a href="#">Steps</a></li>
                        <li class="list-group-item"><a href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                @endif
                @if(auth()->user()->hasRole('client'))
                    <!-- Menu Client -->
                    <ul class="list-group">
                        <li class="list-group-item"><a href="{{ route('users.index') }}">My Courses</a></li>
                        <li class="list-group-item"><a href="#">My Paths</a></li>
                        <li class="list-group-item"><a href="#">My CV</a></li>
                        <li class="list-group-item"><a href="#">My Files</a></li>
                        <li class="list-group-item"><a href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                @endif
            </div>
            <div class="col-md-9">
                <h1>Dashboard</h1>
            </div>
        </div>
    </div>
@endsection
