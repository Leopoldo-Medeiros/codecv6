@extends('layouts.admin')
@if(isset($user))
    @section('title', 'Edit user')
@else
    @section('title', 'Add user')
@endif
@section('content')
    <section class="bg-light py-3 py-md-5 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    <h3 class="fs-4 text-secondary mb-3 text-uppercase text-center">{{ isset($user) ? 'Edit User' : 'Create User' }}</h3>
                    <hr class="w-40 mx-auto mb-4 border-dark-subtle">
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="bg-white border rounded shadow-sm overflow-hidden p-4 p-xl-5">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as  $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="login100-form validate-form" action="{{ isset($user) ? route('users.update', ['user' => $user->id]) : route('users.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateFullName()">
                            @csrf
                            @if(isset($user))
                                @method('PUT')
                            @endif

                            <div class="row gy-2 gy-xl-3">
                                <div class="col-10">
                                    <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" class="form-control" id="fullname" name="fullname" value="{{ old('fullname', isset($user) ? $user->fullname : '') }}" required>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-10">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-10">
                                    <label for="profile[birth_date]" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control" id="profile[birth_date]" name="profile[birth_date]" value="{{ old('profile.birth_date', isset($user) ? $user->profile->birth_date : '') }}" required>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-10">
                                    <label for="profile[profession]" class="form-label">Profession <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-briefcase"></i>
                                        </span>
                                        <input type="text" class="form-control" id="profile[profession]" name="profile[profession]" value="{{ old('profile.profession', isset($user) ? $user->profile->profession : '') }}" required>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-10">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <div class="avatar-wrapper">
                                        <img class="profile-pic" src="{{ old('profile.profile_image', empty($user->profile->profile_image) ? '' : Storage::url($user->profile->profile_image)) }}" />
                                        <div class="upload-button">
                                            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                                        </div>
                                        <input class="file-upload" type="file" name="profile_image" accept="image/*"/>
                                    </div>
                                    <div class="feedback"><br/>Use square images. <br/>E.g. 75x75px or 150x150px for better perspective</div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-10">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password" {{ isset($user) ? '' : 'required' }}>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-10">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ isset($user) ? '' : 'required' }}>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-10">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person-badge"></i>
                                        </span>
                                        <select class="form-control" name="role" id="role" required>
                                            <option selected disabled value="">Choose Role...</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ old('role', isset($user) && $user->roles->isNotEmpty() ? $user->roles->first()->id : '') == $role->id ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-1">
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg fw-bold" type="submit">{{ isset($user) ? 'Update' : 'Create' }}</button>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg fw-bold mt-4">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </a> <!-- Back button -->
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function validateFullName() {
            const fullNameInput = document.getElementById('fullname');
            const fullName = fullNameInput.value.trim();

            if (fullName.split(' ').length < 2) {
                alert('Full Name must contain at least two words.');
                return false;
            }
            return true;
        }
        $(document).ready(function() {

            var readURL = function(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('.profile-pic').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(".file-upload").on('change', function(){
                readURL(this);
            });

            $(".upload-button").on('click', function() {
                $(".file-upload").click();
            });
        });
    </script>
@endsection
