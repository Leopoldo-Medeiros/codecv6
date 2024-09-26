<nav class="navbar bg-white topbar border-bottom static-top p-0 pt-1 m-0">
    <div class="container-fluid d-flex justify-content-between align-items-center pe-4">
        <!-- Left side content if needed -->
        <!-- Profile Section (Aligned Right) -->
        <div class="dropdown ms-auto d-flex align-items-center">
            <div class="mode-switch d-flex align-items-center p-2 me-3">
                <button title="Use dark mode" id="darkModeSwitch" class="btn btn-sm btn-outline-secondary rounded-circle me-2">
                    <img src="{{ asset('images/custom-moon-icon.svg') }}" alt="Dark Mode" class="custom-icon">
                </button>
                <button title="Use light mode" id="lightModeSwitch" class="btn btn-sm btn-outline-secondary rounded-circle me-2">
                    <img src="{{ asset('images/custom-sun-icon.svg') }}" alt="Light Mode" class="custom-icon">
                </button>
            </div>
            <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
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
</nav>
