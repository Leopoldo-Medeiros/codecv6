<li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
        <i class="fas fa-graduation-cap mt-4 mr-2"></i> My Courses
    </a>
</li>
<li class="nav-item">
    <a href="#" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
        <i class="fas fa-route mr-2"></i> My Paths
    </a>
</li>
<li class="nav-item">
    <a href="#" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
        <i class="fas fa-id-card mr-2"></i> My CV
    </a>
</li>
<li class="nav-item">
    <a href="#" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
        <i class="fas fa-folder-open mr-2"></i> My Files
    </a>
</li>
<li class="nav-item">
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-client').submit();" class="nav-link bg-blue-400 text-black py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
    </a>
    <form id="logout-form-client" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>
