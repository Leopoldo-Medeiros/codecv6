<nav class="navbar bg-white topbar border-bottom static-top p-0 pt-1 m-0">
    <div class="container-fluid d-flex justify-content-between align-items-center pe-4">
        <!-- Left side content if needed -->
        <!-- Profile Section (Aligned Right) -->
        <div class="dropdown ms-auto d-flex align-items-center">
            <div class="mode-switch d-flex align-items-center p-2 me-3">
                <button title="Toggle dark mode" id="themeSwitch" class="btn btn-sm btn-outline-secondary rounded-circle me-2">
                    <i id="themeIcon" class="bi"></i>
                </button>
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
                        <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('logout') }}">
                            <i class="fas fa-sign-out-alt me-2"></i> Sign out
                        </a>
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
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon');
            } else {
                themeIcon.classList.remove('bi-moon');
                themeIcon.classList.add('bi-sun');
            }
        }
    });
</script>
