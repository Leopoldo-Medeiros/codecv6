<header>
    <!-- Navigation bar -->
    <nav class="relative container mx-auto p-6">
        <!-- Flex container -->
        <div class="flex flex-auto items-center justify-between">
            <!-- Logo -->
            <a class="logo" href="/">
                <div class="pt-2">
                    <span class="text-3xl font-bold text-gray-600">code</span>
                    <span class="text-3xl font-bold text-orange-600">CV</span>
                </div>
            </a>
            <!-- Menu Items -->
            <div class="hidden space-x-6 md:flex">
                <a class="hover:text-darkGrayishBlue" href="/">Home</a>
                <a class="hover:text-darkGrayishBlue" href="/services">Services</a>
                <a class="hover:text-darkGrayishBlue" href="/testimonial">Testimonials</a>
                <a class="hover:text-darkGrayishBlue" href="/about">About</a>
                <!--            <a href="#" class="hover:text-darkGrayishBlue">Careers</a>-->
                <!--            <a href="#" class="hover:text-darkGrayishBlue">Community</a>-->
            </div>
        </div>

        <!-- Hamburger Icon-->
        <div class="flex justify-end">
            <button id="menu-btn" class="block hamburger md:hidden focus:outline-none">
                <span class="hamburger-top"></span>
                <span class="hamburger-middle"></span>
                <span class="hamburger-bottom"></span>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden">
            <div id="menu" class="absolute flex-col items-center hidden self-end py-8 mt-10 space-y-6 font-bold bg-white sm:w-auto sm:self-center left-6 right-6 drop-shadow-md">
                <a class="hover:text-darkGrayishBlue" href="/">Home</a>
                <a class="hover:text-darkGrayishBlue" href="/services">Services</a>
                <a class="hover:text-darkGrayishBlue" href="/testimonial">Testimonials</a>
                <a class="hover:text-darkGrayishBlue" href="/about">About</a>
                <!--            <a href="#" class="hover:text-darkGrayishBlue">About Us</a>-->
                <!--            <a href="#" class="hover:text-darkGrayishBlue">Careers</a>-->
                <!--            <a href="#" class="hover:text-darkGrayishBlue">Community</a>-->
            </div>
        </div>

        <!-- Login section -->
        <div class="navigation">
            @php
                use Illuminate\Support\Facades\Auth;
            @endphp

            @if (Auth::check())
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
            @endif
        </div>
        <!-- End Login -->

        <!-- Register section -->
        <div class="navigation">
            @php
                use Illuminate\Http\Request;
            @endphp

            @if (Auth::check())
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('register') }}">Register</a>
            @endif
        </div>
        <!-- End Register -->
    </nav>
</header>

{{--    <nav class="relative flex w-full items-center justify-between bg-white py-2 text-neutral-600 shadow-lg hover:text-neutral-700 focus:text-neutral-700 dark:bg-neutral-600 dark:text-neutral-200 md:flex-wrap md:justify-start"--}}
{{--        data-te-navbar-ref>--}}
{{--        <div class="flex w-full flex-wrap items-center justify-between px-3">--}}
{{--            <div class="flex items-center">--}}
{{--                <!-- Hamburger menu button -->--}}
{{--                <button--}}
{{--                    class="border-0 bg-transparent px-2 text-xl leading-none transition-shadow duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 dark:hover:text-white dark:focus:text-white lg:hidden"--}}
{{--                    type="button"--}}
{{--                    data-te-collapse-init--}}
{{--                    data-te-target="#navbarSupportedContentY"--}}
{{--                    aria-controls="navbarSupportedContentY"--}}
{{--                    aria-expanded="false"--}}
{{--                    aria-label="Toggle navigation">--}}
{{--                    <!-- Hamburger menu icon -->--}}
{{--                    <span class="[&>svg]:w-5">--}}
{{--            <svg--}}
{{--                xmlns="http://www.w3.org/2000/svg"--}}
{{--                fill="none"--}}
{{--                viewBox="0 0 24 24"--}}
{{--                stroke-width="1.5"--}}
{{--                stroke="currentColor"--}}
{{--                class="h-7 w-7">--}}
{{--              <path--}}
{{--                  stroke-linecap="round"--}}
{{--                  stroke-linejoin="round"--}}
{{--                  d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />--}}
{{--            </svg>--}}
{{--          </span>--}}
{{--                </button>--}}
{{--            </div>--}}

{{--            <!-- Navigation links -->--}}
{{--            <div--}}
{{--                class="!visible hidden grow basis-[100%] items-center lg:!flex lg:basis-auto"--}}
{{--                id="navbarSupportedContentY"--}}
{{--                data-te-collapse-item>--}}
{{--                <ul--}}
{{--                    class="mr-auto flex flex-col lg:flex-row"--}}
{{--                    data-te-navbar-nav-ref>--}}
{{--                    <li class="mb-4 lg:mb-0 lg:pr-2" data-te-nav-item-ref>--}}
{{--                        <a class="block transition duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 disabled:text-black/30 dark:hover:text-white dark:focus:text-white lg:p-2 [&.active]:text-black/90"--}}
{{--                            href="/"--}}
{{--                            data-te-nav-link-ref--}}
{{--                            data-te-ripple-init--}}
{{--                            data-te-ripple-color="light"--}}
{{--                        >Home</a>--}}
{{--                    </li>--}}
{{--                    <li class="mb-4 lg:mb-0 lg:pr-2" data-te-nav-item-ref>--}}
{{--                        <a class="block transition duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 disabled:text-black/30 dark:hover:text-white dark:focus:text-white lg:p-2 [&.active]:text-black/90"--}}
{{--                            href="/services"--}}
{{--                            data-te-nav-link-ref--}}
{{--                            data-te-ripple-init--}}
{{--                            data-te-ripple-color="light"--}}
{{--                        >Services</a>--}}
{{--                    </li>--}}
{{--                    <li class="mb-4 lg:mb-0 lg:pr-2" data-te-nav-item-ref>--}}
{{--                        <a--}}
{{--                            class="block transition duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 disabled:text-black/30 dark:hover:text-white dark:focus:text-white lg:p-2 [&.active]:text-black/90"--}}
{{--                            href="/testimonial"--}}
{{--                            data-te-nav-link-ref--}}
{{--                            data-te-ripple-init--}}
{{--                            data-te-ripple-color="light"--}}
{{--                        >Testimonials</a--}}
{{--                        >--}}
{{--                    </li>--}}
{{--                    <li class="mb-2 lg:mb-0 lg:pr-2" data-te-nav-item-ref>--}}
{{--                        <a--}}
{{--                            class="block transition duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 disabled:text-black/30 dark:hover:text-white dark:focus:text-white lg:p-2 [&.active]:text-black/90"--}}
{{--                            href="/about"--}}
{{--                            data-te-nav-link-ref--}}
{{--                            data-te-ripple-init--}}
{{--                            data-te-ripple-color="light"--}}
{{--                        >About</a--}}
{{--                        >--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </div>--}}

{{--    </nav>--}}
</header>
