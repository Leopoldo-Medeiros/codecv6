<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href="{{ asset('../css/app.css') }}" rel="stylesheet">


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item dropdown">
        <a class="btn btn--blue-400 dropdown-toggle px-4" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            MENU
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item hover--blue-200" href="{{ route('users.index') }}">Clients</a></li>
            <li><a class="dropdown-item hover--blue-200" href="#">Courses</a></li>
            <li><a class="dropdown-item hover--blue-200" href="#">Paths</a></li>
            <li><a class="dropdown-item hover--blue-200" href="#">Steps</a></li>
            <li><a class="dropdown-item hover--blue-200" href="{{ route('logout') }}">Logout</a></li>
        </ul>
    </li>
</ul>
