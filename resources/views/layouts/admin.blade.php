<!-- resources/views/layouts/admin.blade.php -->

@include('includes.admin.header')
@include('includes.admin.menu.navbar')

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">

<!-- Begin page content -->
<div class="container-fluid p-0 flex-shrink-0">
    <div class="row flex-nowrap">
        @if (!request()->is('register'))
            <div class="col-auto col-md-3 col-xl-2 pr-sm-2 px-0 pl-0 custom-menu-bg">
                <div id="sidebar"
                     class="custom-menu-bg custom-sidebar-width d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 text-white vh-100">
                    
                    @if(Auth::check())
                    <div class="user-profile my-3 w-100 text-center">
                        <div class="d-flex justify-content-center">
                            <div class="profile-image-container mb-3">
                                <img src="{{ Auth::user()->profile && Auth::user()->profile->profile_image ? Storage::url(Auth::user()->profile->profile_image) : asset('images/team-13.jpg') }}" 
                                    alt="{{ Auth::user()->fullname }}" 
                                    class="profile-image">
                            </div>
                        </div>
                        <h6 class="mb-1 fw-bold">{{ Auth::user()->fullname }}</h6>
                        <p class="text-muted small mb-3">
                            @if(Auth::user()->hasRole('admin'))
                                <span class="badge bg-primary-subtle text-primary">Admin</span>
                            @else
                                <span class="badge bg-info-subtle text-info">{{ Auth::user()->profile && Auth::user()->profile->profession ? Auth::user()->profile->profession : 'Member' }}</span>
                            @endif
                        </p>
                        <div class="d-flex justify-content-center gap-2 mb-2">
                            <a href="{{ route('profile') }}" class="btn btn-sm btn-light" title="View Profile">
                                <i class="fas fa-user"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-light" title="Settings">
                                <i class="fas fa-cog"></i>
                            </a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="btn btn-sm btn-light" title="Logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <hr class="w-100 my-2 opacity-25">
                    @endif
                    
                    <ul class="nav flex-column">
                        @if (Auth::check())
                            @role('client')
                            @include('includes.admin.menu.client')
                        @else
                            @include('includes.admin.menu.admin')
                            @endrole
                        @endif
                    </ul>
                </div>
            </div>
        @endif
        <div class="col py-3">
            @yield('content')
        </div>
    </div>
</div>

@include('includes.admin.footer')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const htmlElement = document.documentElement;
        const darkModeSwitch = document.getElementById('darkModeSwitch');
        const lightModeSwitch = document.getElementById('lightModeSwitch');

        // Set the default theme to dark if no setting is found in local storage
        const currentTheme = localStorage.getItem('bsTheme') || 'dark';
        htmlElement.setAttribute('data-bs-theme', currentTheme);

        darkModeSwitch.addEventListener('click', function () {
            htmlElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('bsTheme', 'dark');
        });

        lightModeSwitch.addEventListener('click', function () {
            htmlElement.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('bsTheme', 'light');
        });
    });
</script>

<script src="{{ asset('js/wow.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
