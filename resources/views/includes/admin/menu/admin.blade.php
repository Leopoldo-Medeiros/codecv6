<!-- resources/views/includes/admin/menu/admin.blade.php -->

<li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link text-dark large-font">
        <i class="fas fa-users me-2"></i>Clients
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('courses.index') }}" class="nav-link text-dark large-font">
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
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-admin').submit();" class="nav-link text-dark large-font">
        <i class="fas fa-sign-out-alt me-2"></i>
        Logout
    </a>
    <form id="logout-form-admin" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>

<!-- Notification dropdown -->
<div class="nav-item dropdown me-3">
    <a class="nav-link dropdown-toggle hidden-arrow position-relative" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
