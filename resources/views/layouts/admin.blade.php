<!-- resources/views/layouts/admin.blade.php -->
@include('includes.admin.header')
@include('includes.admin.menu.navbar')

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Stylesheets -->
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/contacts/contact-4/assets/css/contact-4.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

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
<script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
<script src="https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
