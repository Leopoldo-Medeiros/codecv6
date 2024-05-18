{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @role('admin')
                <div class="menu-admin">
                    <ul>
                        <li><a href="#">Link 1</a></li>
                        <li><a href="#">Link 2</a></li>
                        <li><a href="#">Link 3</a></li>
                    </ul>
                </div>
                @else
                    <div class="menu-users">
                        <ul>
                            <li><a href="#">User Link 1</a></li>
                            <li><a href="#">User Link 2</a></li>
                            <li><a href="#">User Link 3</a></li>
                        </ul>
                    </div>
                    @endrole
            </div>
            <div class="col-md-9">
                <h1>Dashboard</h1>
            </div>
        </div>
    </div>
@endsection
