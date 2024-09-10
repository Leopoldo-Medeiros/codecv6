<!-- resources/views/includes/admin/menu/admin.blade.php -->
<div id="sidebar" class="ms-3 custom-menu-bg">
    <div class="sidebar-header">
        <h3 class="mt-3 fw-bold ms-4">MENU</h3>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link text-dark large-font">
                <i class="fas fa-users me-2"></i>
                Clients
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
