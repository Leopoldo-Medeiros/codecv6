<nav class="navbar bg-white topbar border-bottom shadow-sm py-2">
    <div class="container-fluid">
        <!-- Sidebar toggle button (if needed) - hidden on larger screens -->
        <button type="button" id="sidebarCollapse" class="btn d-md-none">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Logo moved to center-left -->
        <div class="navbar-brand py-0 ms-md-5 mx-auto mx-md-0">
            <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="smll-logo">
                <img src="{{ asset('images/codecv.png') }}" alt="Logo" class="navbar-logo">
            </a>
        </div>
        
        <!-- Right side controls -->
        <div class="dropdown ms-auto d-flex align-items-center">
            <div class="mode-switch d-flex align-items-center p-2 me-3">
                <button title="Toggle dark mode" id="themeSwitch" class="btn btn-sm btn-outline-secondary rounded-circle me-2">
                    <i id="themeIcon" class="bi"></i>
                </button>
            </div>
            
            <!-- Notification dropdown -->
            <div class="nav-item dropdown me-3">
                <a class="nav-link dropdown-toggle hidden-arrow position-relative notification-link" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="badge rounded-pill badge-notification bg-danger">1</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdownMenuLink">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item" href="#">New course available</a></li>
                    <li><a class="dropdown-item" href="#">Certificate ready</a></li>
                    <li><a class="dropdown-item" href="#">System update</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-primary" href="#">View all notifications</a></li>
                </ul>
            </div>
            
            @if(Auth::check())
                <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ Auth::user()->profile && Auth::user()->profile->profile_image ? Storage::url(Auth::user()->profile->profile_image) : asset('images/team-13.jpg') }}" alt="avatar" width="32" height="32" class="rounded-circle">
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
                        <a class="dropdown-item d-flex align-items-center text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Sign out
                        </a>
                        <form id="logout-form-navbar" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            @else
                <a class="nav-link" href="{{ route('login') }}">Login</a>
            @endif
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const htmlElement = document.documentElement;
        const themeSwitch = document.getElementById('themeSwitch');
        const themeIcon = document.getElementById('themeIcon');

        // Set the default theme to dark if no setting is found in local storage
        const currentTheme = localStorage.getItem('bsTheme') || 'dark';
        htmlElement.setAttribute('data-bs-theme', currentTheme);
        updateIcon(currentTheme);

        themeSwitch.addEventListener('click', function () {
            const newTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('bsTheme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('bi-moon');
                themeIcon.classList.add('bi-sun');
            } else {
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon');
            }
        }
    });
</script>
