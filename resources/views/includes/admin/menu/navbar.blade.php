<header class=" border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid p-0">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/logo/codecv2.png') }}" style="width: 150px;" alt="logo">
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="dropdown ms-auto">
            <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
            </a>
            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                <li><a class="dropdown-item" href="#">New project...</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('logout') }}">Sign out</a></li>
            </ul>
        </div>
    </div>
</header>
