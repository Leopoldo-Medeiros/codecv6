<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="DSAThemes">

    <meta name="description" content="CODECV - IT Consultancy Career">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SITE TITLE -->
    <title>CODECV- @yield('title')</title>

    <!-- FAVICON AND TOUCH ICONS -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('images/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('images/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('images/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('images/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('images/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">

    <!-- BOOTSTRAP CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- FONT ICONS -->
    <link href="{{ asset('css/flaticon.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


    <!-- PLUGINS STYLESHEET -->
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    <link id="effect" href="{{ asset('css/dropdown-effects/fade-down.css') }}" media="all" rel="stylesheet">
    <link href="{{ asset('css/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('css/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/lunar.css') }}" rel="stylesheet">

    <!-- ON SCROLL ANIMATION -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">

    <!-- TEMPLATE CSS -->
    <link href="{{ asset('css/pink-theme.css') }}" rel="stylesheet">

    <!-- RESPONSIVE CSS -->
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">

    <!-- CODECV CSS -->
{{--    <link href="{{ asset('css/codecv5.css') }}" rel="stylesheet">--}}

</head>
<!-- ... Rest of your HTML ... -->

<body>

<!-- PRELOADER SPINNER
		============================================= -->
<div id="loading" class="loading--theme">
    <div id="loading-center"><span class="loader"></span></div>
</div>

<!-- PAGE CONTENT
============================================= -->
<div id="page" class="page font--jakarta">

    <!-- HEADER
			============================================= -->
    <header id="header" class="tra-menu navbar-light white-scroll">
        <div class="header-wrapper">


            <!-- NAVIGATION MENU -->
            <div class="wsmainfull menu clearfix">
                <div class="wsmainwp clearfix">


{{--                    <!-- HEADER BLACK LOGO -->--}}
{{--                    <div class="desktoplogo">--}}
{{--                        <a href="{{ route('home') }}" class="logo-black"><img src="{{ asset('images/codecv-logo-258x79.png') }}" alt="logo"></a>--}}
{{--                    </div>--}}


{{--                    <!-- HEADER WHITE LOGO -->--}}
{{--                    <div class="desktoplogo">--}}
{{--                        <a href="{{ route('home') }}" class="logo-white"><img src="{{ asset('images/codecv-logo-258x79.png') }}" alt="logo"></a>--}}
{{--                    </div>--}}


                    <!-- MAIN MENU -->
                    <nav class="wsmenu clearfix">
                        <ul class="wsmenu-list nav-theme">

                            <!-- SIMPLE NAVIGATION LINK -->
{{--                            <li class="nl-simple" aria-haspopup="true"><a href="{{ route('pricing') }}" class="h-link">Pricing</a></li>--}}


                            <!-- SIMPLE NAVIGATION LINK -->
{{--                            <li class="nl-simple" aria-haspopup="true"><a href="{{ route('faqs') }}" class="h-link">FAQs</a></li>--}}

                        </ul>
                    </nav>	<!-- END MAIN MENU -->


                </div>
            </div>	<!-- END NAVIGATION MENU -->


        </div>     <!-- End header-wrapper -->
    </header>	<!-- END HEADER -->

    <!-- ... (Continue with the rest of your content) ... -->
    @yield('content')

    @include('layouts.footer')

</div>


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

</body>

</html>
