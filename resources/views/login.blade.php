@section('title', 'Login')

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
        <!-- LOGIN PAGE
    ============================================= -->
        <div id="login" class="bg--fixed login-1 login-section division">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-lg-6 offset-md-2 offset-lg-3">
                        <div class="register-page-form">


                            <!-- TITLE -->
                            <div class="col-md-12">
                                <div class="register-form-title">
                                    <h3 class="s-32 w-700">Log in to CODECV</h3>
                                </div>
                            </div>


                            <!-- LOGIN FORM -->
                            <form name="signinform" method="POST" class="row sign-in-form"
                                  action="{{ route('login.post') }}">
                                @csrf

                                <!-- Google Button -->
                                <div class="col-md-12">
                                    <a href="#" class="btn btn-google ico-left">
                                        <img src="{{ asset('images/png_icons/google.png') }}" alt="google-icon"> Sign in with Google
                                    </a>
                                </div>

                                <!-- Login Separator -->
                                <div class="col-md-12 text-center">
                                    <div class="separator-line">Or, sign in with your email</div>
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

                                <!-- Reset Password Link -->
                                {{-- <div class="col-md-12">--}}
                                {{--    <div class="reset-password-link">--}}
                                {{--       <p class="p-sm"><a href="reset-password.html" class="color--theme">Forgot your password?</a></p>--}}
                                {{--    </div>--}}
                                {{-- </div>--}}

                                <!-- Form Submit Button -->
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn--theme hover--theme submit">Log In</button>
                                </div>

                            </form>    <!-- END LOGIN FORM -->


                        </div>
                    </div>
                </div>       <!-- End row -->
            </div>       <!-- End container -->
        </div>    <!-- END LOGIN PAGE -->
    </div>    <!-- END PAGE CONTENT -->
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
