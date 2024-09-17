<!-- resources/views/layouts/admin.blade.php -->
@include('includes.admin.header')
@include('includes.admin.menu.navbar')

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Stylesheets -->
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
<script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>

<!-- Begin page content -->
<div class="container-fluid p-0 flex-shrink-0">
    <div class="d-grid gap-6" style="grid-template-columns: 1fr 5fr;">
        @role('client')
        @include('includes.admin.menu.client')
        @else
            @include('includes.admin.menu.admin')
            @endrole
            <div class="p-3">
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
