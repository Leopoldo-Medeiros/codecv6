@section('title', 'Register')

@include('includes.site.header')

<!-- PRELOADER SPINNER
		============================================= -->
<div id="loading" class="loading--theme">
    <div id="loading-center"><span class="loader"></span></div>
</div>

<!-- PAGE CONTENT
============================================= -->
<div id="page" class="page font--jakarta">

    <!-- ... (Continue with the rest of your content) ... -->
    <!-- REGISTER PAGE
============================================= -->
    <div id="register" class="bg--fixed register-1 register-section division">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-lg-6 offset-md-2 offset-lg-3">
                    <div class="register-page-form">

                        <!-- TITLE -->
                        <div class="col-md-12">
                            <div class="register-form-title">
                                <h3 class="s-32 w-700">Register to CODECV</h3>
                            </div>
                        </div>

                        <!-- REGISTER FORM -->
                        <form name="registerform" method="POST" class="row register-form" action="{{ route('register') }}">
                            @csrf

                            <!-- Google Button -->
                            <div class="col-md-12">
                                <a href="#" class="btn btn-google ico-left">
                                    <img src="{{ asset('images/png_icons/google.png') }}" alt="google-icon"> Sign up with Google
                                </a>
                            </div>

                            <!-- Register Separator -->
                            <div class="col-md-12 text-center">
                                <div class="separator-line">Or, sign up with your email</div>
                            </div>

                            @if ($errors->any())
                                <div class="col-md-12 text-center">
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li class="text-danger">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Form Input -->
                            <div class="col-md-12">
                                <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input class="form-control fullname" type="text" name="fullname" placeholder="Your Name">
                            </div>

                            <!-- Form Input -->
                            <div class="col-md-12">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <input class="form-control email" type="email" name="email" placeholder="example@example.com">
                            </div>

                            <!-- Form Input -->
                            <div class="col-md-12">
                                <p class="p-sm input-header">Password</p>
                                <div class="wrap-input">
                                    <span class="btn-show-pass ico-20"><span class="flaticon-visibility eye-pass"></span></span>
                                    <input class="form-control password" type="password" name="password" placeholder="* * * * * * * * *">
                                </div>
                            </div>

                            <!-- Form Input -->
                            <div class="col-md-12">
                                <p class="p-sm input-header">Confirm Password</p>
                                <div class="wrap-input">
                                    <span class="btn-show-pass ico-20"><span class="flaticon-visibility eye-pass"></span></span>
                                    <input class="form-control password_confirmation" type="password" name="password_confirmation" placeholder="* * * * * * * * *">
                                </div>
                            </div>

                            <!-- Form Submit Button -->
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--theme hover--theme submit">Register</button>
                            </div>

                        </form>    <!-- END REGISTER FORM -->

                    </div>
                </div>
            </div>       <!-- End row -->
        </div>       <!-- End container -->
    </div>    <!-- END REGISTER PAGE -->
</div>    <!-- END PAGE CONTENT -->

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
