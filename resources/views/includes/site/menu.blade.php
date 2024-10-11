<!-- HEADER
			============================================= -->
<header id="header" class="tra-menu navbar-light white-scroll">
    <div class="header-wrapper">


        <!-- MOBILE HEADER -->
        <div class="wsmobileheader clearfix">
            <span class="smllogo"><img src="{{ asset('images/logo/codecv-black.png') }}" alt="mobile-logo"></span>
            <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
        </div>


        <!-- NAVIGATION MENU -->
        <div class="wsmainfull menu clearfix">
            <div class="wsmainwp clearfix">


                <!-- HEADER BLACK LOGO -->
                <div class="desktoplogo">
                    <a href="#hero-1" class="logo-black"><img src="{{ asset('images/logo/codecv2.png') }}" alt="logo"></a>
                </div>


                <!-- HEADER WHITE LOGO -->
                <div class="desktoplogo">
                    <a href="#hero-1" class="logo-white"><img src="{{ asset('images/logo/codecv-black.png') }}" alt="logo"></a>
                </div>


                <!-- MAIN MENU -->
                <nav class="wsmenu clearfix">
                    <ul class="wsmenu-list nav-theme">


                        <!-- DROPDOWN SUB MENU -->
                        <li aria-haspopup="true"><a href="#" class="h-link">About <span class="wsarrow"></span></a></li>


                        <!-- SIMPLE NAVIGATION LINK -->
                        <li class="nl-simple" aria-haspopup="true"><a href="#features-6" class="h-link">Features</a></li>


                        <!-- SIMPLE NAVIGATION LINK -->
                        <li class="nl-simple" aria-haspopup="true"><a href="pricing-1.html" class="h-link">Pricing</a></li>


                        <!-- SIMPLE NAVIGATION LINK -->
                        <li class="nl-simple" aria-haspopup="true"><a href="#faqs-3" class="h-link">FAQs</a></li>


                        @if (Auth::check())
                            <li class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
                                <a href="{{ route('dashboard') }}" class="h-link">Dashboard</a>
                            </li>
                            <li class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
                                <a href="{{ route('logout') }}" class="h-link">Log out</a>
                            </li>
                        @else
                            <li class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
                                <a href="{{ route('login') }}" class="h-link">Login</a>
                            </li>
                        @endif


                        <!-- SIGN UP BUTTON -->
                        <li class="nl-simple" aria-haspopup="true">
                            <a href="{{ route('login') }}" class="btn r-04 btn--theme hover--tra-white last-link">Sign up</a>
                        </li>


                    </ul>
                </nav>	<!-- END MAIN MENU -->


            </div>
        </div>	<!-- END NAVIGATION MENU -->


    </div>     <!-- End header-wrapper -->
</header>	<!-- END HEADER -->
