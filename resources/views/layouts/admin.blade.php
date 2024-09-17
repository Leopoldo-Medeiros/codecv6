<!-- resources/views/layouts/admin.blade.php -->
@include('includes.admin.header')
@include('includes.admin.menu.navbar')

<!-- Begin page content -->
<div class="container-fluid p-0 flex-shrink-0">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 pr-sm-2 px-0 pl-0">
            <div id="sidebar" class="custom-menu-bg custom-sidebar-width d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white vh-90">
                <div class="sidebar-header d-flex justify-content-center align-items-center logo-background w-100">
                    <img src="{{ asset('images/logo/codecv.png') }}" alt="Logo" class="logo-img">
                </div>
                <ul class="nav flex-column">
                    @role('client')
                        @include('includes.admin.menu.client')
                    @else
                        @include('includes.admin.menu.admin')
                    @endrole
                </ul>
            </div>
        </div>
        <div class="col py-3">
            @yield('content')
        </div>
    </div>
</div>

@include('includes.admin.footer')

<!-- External Scripts -->
<script src="https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/wow.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>

</body>
</html>
