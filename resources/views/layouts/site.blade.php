@include('includes.site.header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('css/codecv5.css') }}">
<!-- PRELOADER SPINNER ============================================= --><!-- PRELOADER SPINNER ============================================= -->
<div id="loading" class="loading--theme">
    <div id="loading-center"><span class="loader"></span>
    </div>
</div>

<!-- PAGE CONTENT ============================================= -->
<div id="page" class="page font--jakarta">

    @include('includes.site.menu')

    <!-- ... (Continue with the rest of your content) ... -->
    @yield('content')

</div>
@include('includes.site.footer')

<!-- EXTERNAL SCRIPTS
============================================= -->
<script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/modernizr.custom.js') }}"></script>
<script src="{{ asset('js/jquery.easing.js') }}"></script>
<script src="{{ asset('js/jquery.appear.js') }}"></script>
<script src="{{ asset('js/menu.js') }}"></script>
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
