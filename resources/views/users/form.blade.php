@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST" onsubmit="return validateFullName()">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control mb-3" id="fullname" name="fullname" value="{{ old('fullname', isset($user) ? $user->fullname : '') }}" required>
                <div id="fullname-warning" class="text-danger" style="display:none;">Please enter your full name.</div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control mb-3" id="email" name="email" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
            </div>

            <div class="form-group">
                <label for="profile[birth_date]">Birth Date</label>
                <input type="date" class="form-control mb-3" id="profile[birth_date]" name="profile[birth_date]" value="{{ old('profile.birth_date', isset($user) ? $user->profile->birth_date : '') }}">
            </div>

            <div class="form-group">
                <label for="profile[profession]">Profession</label>
                <input type="text" class="form-control mb-3" id="profile[profession]" name="profile[profession]" value="{{ old('profile.profession', isset($user) ? $user->profile->profession : '') }}">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control mb-3" id="password" name="password" value="" {{ isset($user) ? '' : 'required' }}>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control mb-3" id="password_confirmation" name="password_confirmation" value="" {{ isset($user) ? '' : 'required' }}>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" class="form-control custom-width mb-3">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role', isset($user) && $user->roles->isNotEmpty() ? $user->roles->first()->id : '') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">{{ isset($user) ? 'Update' : 'Create' }}</button>
        </form>
    </div>

    {{-- This script will display a warning message if the full name input does not contain at least two words.--}}
    <script>
        function validateFullName() {
            const fullNameInput = document.getElementById('fullname');
            const fullNameWarning = document.getElementById('fullname-warning');
            const fullName = fullNameInput.value.trim();

            if (fullName.split(' ').length < 2) {
                fullNameWarning.style.display = 'block';
                return false;
            } else {
                fullNameWarning.style.display = 'none';
                return true;
            }
        }
    </script>
@endsection
