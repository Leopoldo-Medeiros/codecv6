<nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
    <div class="container-fluid border-bottom px-2">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Profile Section -->
            <div class="dropdown ms-auto">
                <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle mb-2" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $user->profile->profile_image ? Storage::url($user->profile->profile_image) : asset('images/team-13.jpg') }}" alt="avatar" width="32" height="32" class="rounded-circle">
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser2">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="fas fa-folder me-2"></i> New project...
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('logout') }}">
                            <i class="fas fa-sign-out-alt me-2"></i> Sign out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
