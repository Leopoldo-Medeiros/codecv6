@include('includes.admin.header')

@include('includes.admin.menu.navbar')

<!-- Begin page content -->
<div class="container-fluid p-0 flex-shrink-0">
    <div class="d-grid gap-6" style="grid-template-columns: 1fr 5fr;">
        @include('includes.admin.menu')
        <div class="p-3">
            @yield('content')
        </div>
    </div>
</div>

@include('includes.admin.footer')

</body>

<!-- EXTERNAL SCRIPTS
============================================= -->
<script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/modernizr.custom.js') }}"></script>
<script src="{{ asset('js/jquery.easing.js') }}"></script>
<script src="{{ asset('js/jquery.appear.js') }}"></script>
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('js/pricing-toggle.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/request-form.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/lunar.js') }}"></script>
<script src="{{ asset('js/wow.js') }}"></script>

<!-- Custom Script -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</html>
