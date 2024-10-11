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
                     class="custom-menu-bg custom-sidebar-width d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white vh-100">
                    <div class="sidebar-header d-flex justify-content-center align-items-center logo-background w-100">
                        <img src="{{ asset('images/logo/codecv.png') }}" alt="Logo" class="logo-img mx-auto">
                    </div>
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
