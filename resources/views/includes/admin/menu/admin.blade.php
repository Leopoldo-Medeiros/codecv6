<div id="sidebar" class="ms-3 custom-menu-bg custom-sidebar-width">
    <div class="sidebar-header d-flex justify-content-center align-items-center logo-background">
        <img src="{{ asset('images/logo/codecv.png') }}" alt="Logo" class="logo-img">
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link text-dark large-font">
                <i class="fas fa-users me-2"></i>Clients
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link text-dark large-font">
                <i class="fas fa-book me-2"></i>
                Courses
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link text-dark large-font">
                <i class="fas fa-map-signs me-2"></i>
                Paths
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link text-dark large-font">
                <i class="fas fa-tasks me-2"></i>
                Steps
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link text-dark large-font">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link text-dark large-font">
                <i class="fas fa-sign-out-alt me-2"></i>
                Logout
            </a>
        </li>
    </ul>
</div>
