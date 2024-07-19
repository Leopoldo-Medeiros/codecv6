<div id="sidebar">
    <div class="sidebar-header">
        <h3 class="text-center">Menu</h3>
    </div>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                <i class="fas fa-users mt-4 mr-2"></i> Clients
            </a>
        </li>
        <li>
            <a href="#" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                <i class="fas fa-book mr-2"></i> Courses
            </a>
        </li>
        <li>
            <a href="#" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                <i class="fas fa-map-signs mr-2"></i> Paths
            </a>
        </li>
        <li>
            <a href="#" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                <i class="fas fa-tasks mr-2"></i> Steps
            </a>
        </li>
        <li>
            <a href="{{ route('logout') }}" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
