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
    <a href="{{ route('logout') }}" class="nav-link text-dark large-font">
        <i class="fas fa-sign-out-alt me-2"></i>
        Logout
    </a>
</li>
