@extends('layouts.site')

@section('title', 'Welcome CODECV')

@section('content')
    <!-- HERO-1
			============================================= -->
    <section id="hero-1" class="bg--scroll hero-section">
        <div class="container">
            <div class="row d-flex align-items-center">


                <!-- HERO TEXT -->
                <div class="col-md-6">
                    <div class="hero-1-txt color--white wow fadeInRight">

                        <!-- Title -->
                        <h2 class="s-58 w-700">Content is the key to building an audience</h2>

                        <!-- Text -->
                        <p class="p-xl">Mauris donec turpis suscipit sapien ociis sagittis sapien tempor a volute
                            ligula and aliquet tortor
                        </p>

                        <!-- Button -->
                        <a href="#banner-3" class="btn r-04 btn--theme hover--tra-white">Get started for free</a>
                        <p class="p-sm btn-txt ico-15">
                            <span class="flaticon-check"></span> No credit card needed, free 14-day trial
                        </p>

                    </div>
                </div>	<!-- END HERO TEXT -->


                <!-- HERO IMAGE -->
                <div class="col-md-6">
                    <div class="hero-1-img wow fadeInLeft">
                        <img class="img-fluid" src="{{ asset('images/hero-1-img.png') }}" alt="hero-image">
                    </div>
                </div>


            </div>    <!-- End row -->
        </div>	   <!-- End container -->
    </section>	<!-- END HERO-1 -->




    <!-- FEATURES-6
    ============================================= -->
    <section id="features-6" class="py-100 features-section division">
        <div class="container">


            <!-- SECTION TITLE -->
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-9">
                    <div class="section-title mb-70">

                        <!-- Title -->
                        <h2 class="s-50 w-700">Build a customer-centric marketing strategy</h2>

                        <!-- Text -->
                        <p class="s-21 color--grey">Ligula risus auctor tempus magna feugiat lacinia.</p>

                    </div>
                </div>
            </div>


            <!-- FEATURES-6 WRAPPER -->
            <div class="fbox-wrapper text-center">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">


                    <!-- FEATURE BOX #1 -->
                    <div class="col">
                        <div class="fbox-6 fb-1 wow fadeInUp">

                            <!-- Icon -->
                            <div class="fbox-ico ico-55">
                                <div class="shape-ico color--theme">

                                    <!-- Vector Icon -->
                                    <span class="flaticon-graphics"></span>

                                    <!-- Shape -->
                                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                    </svg>

                                </div>
                            </div>	<!-- End Icon -->

                            <!-- Text -->
                            <div class="fbox-txt">
                                <h6 class="s-20 w-700">Market Research</h6>
                                <p>Luctus augue egestas undo ultrice and quisque lacus</p>
                            </div>

                        </div>
                    </div>	<!-- END FEATURE BOX #1 -->


                    <!-- FEATURE BOX #2 -->
                    <div class="col">
                        <div class="fbox-6 fb-2 wow fadeInUp">

                            <!-- Icon -->
                            <div class="fbox-ico ico-55">
                                <div class="shape-ico color--theme">

                                    <!-- Vector Icon -->
                                    <span class="flaticon-idea"></span>

                                    <!-- Shape -->
                                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                    </svg>

                                </div>
                            </div>	<!-- End Icon -->

                            <!-- Text -->
                            <div class="fbox-txt">
                                <h6 class="s-20 w-700">User Experience</h6>
                                <p>Luctus augue egestas undo ultrice and quisque lacus</p>
                            </div>

                        </div>
                    </div>	<!-- END FEATURE BOX #2 -->


                    <!-- FEATURE BOX #3 -->
                    <div class="col">
                        <div class="fbox-6 fb-3 wow fadeInUp">

                            <!-- Icon -->
                            <div class="fbox-ico ico-55">
                                <div class="shape-ico color--theme">

                                    <!-- Vector Icon -->
                                    <span class="flaticon-graphic"></span>

                                    <!-- Shape -->
                                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                    </svg>

                                </div>
                            </div>	<!-- End Icon -->

                            <!-- Text -->
                            <div class="fbox-txt">
                                <h6 class="s-20 w-700">Digital Marketing</h6>
                                <p>Luctus augue egestas undo ultrice and quisque lacus</p>
                            </div>

                        </div>
                    </div>	<!-- END FEATURE BOX #3 -->


                    <!-- FEATURE BOX #4 -->
                    <div class="col">
                        <div class="fbox-6 fb-4 wow fadeInUp">

                            <!-- Icon -->
                            <div class="fbox-ico ico-55">
                                <div class="shape-ico color--theme">

                                    <!-- Vector Icon -->
                                    <span class="flaticon-search-engine-1"></span>

                                    <!-- Shape -->
                                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                    </svg>

                                </div>
                            </div>	<!-- End Icon -->

                            <!-- Text -->
                            <div class="fbox-txt">
                                <h6 class="s-20 w-700">SEO Services</h6>
                                <p>Luctus augue egestas undo ultrice and quisque lacus</p>
                            </div>

                        </div>
                    </div>	<!-- END FEATURE BOX #4 -->


                </div>  <!-- End row -->
            </div>	<!-- END FEATURES-6 WRAPPER -->


        </div>     <!-- End container -->
    </section>	<!-- END FEATURES-6 -->




    <!-- DIVIDER LINE -->
    <hr class="divider">




    <!-- TEXT CONTENT
    ============================================= -->
    <section id="lnk-1" class="pt-100 ct-02 content-section division">
        <div class="container">


            <!-- SECTION CONTENT (ROW) -->
            <div class="row d-flex align-items-center">


                <!-- IMAGE BLOCK -->
                <div class="col-md-6">
                    <div class="img-block left-column wow fadeInRight">
                        <img class="img-fluid" src="{{ asset('images/img-10.png') }}" alt="content-image">
                    </div>
                </div>


                <!-- TEXT BLOCK -->
                <div class="col-md-6">
                    <div class="txt-block right-column wow fadeInLeft">

                        <!-- Section ID -->
                        <span class="section-id">Enhance Engagement</span>

                        <!-- Title -->
                        <h2 class="s-46 w-700">Engage your most valuable visitors</h2>

                        <!-- Text -->
                        <p>Sodales tempor sapien quaerat ipsum undo congue laoreet turpis neque auctor turpis
                            vitae dolor luctus placerat magna and ligula cursus purus vitae purus an ipsum suscipit
                        </p>

                        <!-- Small Title -->
                        <h5 class="s-24 w-700">Digits that define growth</h5>

                        <!-- List -->
                        <ul class="simple-list">

                            <li class="list-item">
                                <p>Sapien quaerat tempor an ipsum laoreet purus and sapien dolor an ultrice ipsum
                                    aliquam undo congue cursus dolor
                                </p>
                            </li>

                            <li class="list-item">
                                <p class="mb-0">Purus suscipit cursus vitae cubilia magnis volute egestas vitae
                                    sapien turpis ultrice auctor congue magna placerat
                                </p>
                            </li>

                        </ul>

                    </div>
                </div>	<!-- END TEXT BLOCK -->


            </div>	<!-- END SECTION CONTENT (ROW) -->


        </div>	   <!-- End container -->
    </section>	<!-- END TEXT CONTENT -->




    <!-- TEXT CONTENT
    ============================================= -->
    <section class="pt-100 ct-01 content-section division">
        <div class="container">


            <!-- SECTION CONTENT (ROW) -->
            <div class="row d-flex align-items-center">


                <!-- TEXT BLOCK -->
                <div class="col-md-6 order-last order-md-2">
                    <div class="txt-block left-column wow fadeInRight">


                        <!-- TEXT BOX -->
                        <div class="txt-box">

                            <!-- Title -->
                            <h5 class="s-24 w-700">Solution that grows with you</h5>

                            <!-- Text -->
                            <p>Sodales tempor sapien quaerat ipsum undo congue laoreet turpis neque auctor turpis
                                vitae dolor luctus placerat magna and ligula cursus purus vitae purus an ipsum suscipit
                            </p>

                        </div>	<!-- END TEXT BOX -->


                        <!-- TEXT BOX -->
                        <div class="txt-box mb-0">

                            <!-- Title -->
                            <h5 class="s-24 w-700">Connect your data sources</h5>

                            <!-- Text -->
                            <p>Tempor sapien sodales quaerat ipsum undo congue laoreet turpis neque auctor turpis
                                vitae dolor luctus placerat magna and ligula cursus purus an ipsum vitae suscipit
                                purus
                            </p>

                            <!-- List -->
                            <ul class="simple-list">

                                <li class="list-item">
                                    <p>Tempor sapien quaerat an ipsum laoreet purus and sapien dolor an ultrice ipsum
                                        aliquam undo congue dolor cursus
                                    </p>
                                </li>

                                <li class="list-item">
                                    <p class="mb-0">Cursus purus suscipit vitae cubilia magnis volute egestas vitae
                                        sapien turpis ultrice auctor congue magna placerat
                                    </p>
                                </li>

                            </ul>

                        </div>	<!-- END TEXT BOX -->


                    </div>
                </div>	<!-- END TEXT BLOCK -->


                <!-- IMAGE BLOCK -->
                <div class="col-md-6 order-first order-md-2">
                    <div class="img-block right-column wow fadeInLeft">
                        <img class="img-fluid" src="{{ asset('images/img-06.png') }}" alt="content-image">
                    </div>
                </div>


            </div>	<!-- END SECTION CONTENT (ROW) -->


        </div>	   <!-- End container -->
    </section>	<!-- END TEXT CONTENT -->
@endsection
