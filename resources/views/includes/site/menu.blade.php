<!-- HEADER ============================================= -->
<header id="header" class="tra-menu navbar-light white-scroll">
    <div class="header-wrapper">

        <!-- NAVIGATION MENU -->
        <div class="wsmainfull menu clearfix">
            <div class="wsmainwp clearfix">

                <!-- MOBILE HEADER -->
                <div class="wsmobileheader clearfix">
                    <a href="{{ url('/') }}" class="small-logo">
                        <img src="{{ asset('images/codecv.png') }}" alt="mobile-logo">
                    </a>
                    <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
                </div>

                <!-- DESKTOP HEADER -->
                <div class="wsdesktopheader clearfix float-start ps-sm-5">
                    <a href="{{ url('/') }}" class="smllogo">
                        <img src="{{ asset('images/codecv.png') }}" alt="desktop-logo">
                    </a>
                </div>

                <!-- MAIN MENU -->
                <nav class="wsmenu clearfix">
                    <ul class="wsmenu-list nav-theme">


                        <!-- DROPDOWN SUB MENU -->
                        <li aria-haspopup="true"><a href="{{ route('about-us') }}" class="h-link">About <span class="wsarrow"></span></a></li>

                        <!-- SIMPLE NAVIGATION LINK -->
                        <li class="nl-simple" aria-haspopup="true"><a href="{{ route('pricing') }}" class="h-link">Pricing</a></li>

                        <!-- SIMPLE NAVIGATION LINK -->
                        <li class="nl-simple" aria-haspopup="true"><a href="{{ route('faqs') }}" class="h-link">FAQs</a></li>


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
                            <a href="{{ route('register') }}" class="btn r-04 btn--theme hover--tra-white last-link">Sign up</a>
                        </li>


                    </ul>
                </nav>	<!-- END MAIN MENU -->


            </div>
        </div>	<!-- END NAVIGATION MENU -->


    </div>     <!-- End header-wrapper -->
</header>	<!-- END HEADER -->
