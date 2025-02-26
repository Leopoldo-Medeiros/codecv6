@extends('layouts.site')

@section('title', 'Welcome CODECV')

@section('content')
    <!-- PAGE CONTENT
		============================================= -->
    <div id="page" class="page font--jakarta">

        <!-- HERO-5
        ============================================= -->
        <section id="hero-5" class="bg--scroll hero-section">
            <div class="container">
                <div class="row d-flex align-items-center">


                    <!-- HERO TEXT -->
                    <div class="col-md-6">
                        <div class="hero-5-txt wow fadeInRight">

                            <!-- Title -->
                            <h2 class="s-58 w-700">The growth accelerator for your career</h2>

                            <!-- Text -->
                            <p class="p-lg">We help IT professionals like you define, plan, and achieve their career
                                aspirations.
                            </p>

                            <!-- Button -->
                            <a href="#banner-7" class="btn r-04 btn--theme hover--theme">Get started for free</a>

                        </div>
                    </div>    <!-- END HERO TEXT -->


                    <!-- HERO IMAGE -->
                    <div class="col-md-6">
                        <div class="hero-5-img wow fadeInLeft">
                            <img class="img-fluid" src="{{ asset('images/img-18.png') }}" alt="hero-image">
                        </div>
                    </div>


                </div>    <!-- End row -->
            </div>       <!-- End container -->


            <!-- WAVE SHAPE BOTTOM -->
            <div class="wave-shape-bottom">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 170">
                    <path fill-opacity="1"
                          d="M0,160L120,160C240,160,480,160,720,138.7C960,117,1200,75,1320,53.3L1440,32L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z"></path>
                </svg>
            </div>


        </section>    <!-- END HERO-5 -->


        <!-- DIVIDER LINE -->
        <hr class="divider hr-custom text-center">


        <!-- FEATURES-11
        ============================================= -->
        <section id="features-11" class="pt-100 features-section division">
            <div class="container">


                <!-- SECTION TITLE -->
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-9">
                        <div class="section-title mb-70">

                            <!-- Title -->
                            <h2 class="s-45 w-700">Empowering IT Professionals, Elevating Careers</h2>

                        </div>
                    </div>
                </div>


                <!-- FEATURES-11 WRAPPER -->
                <div class="fbox-wrapper">
                    <div class="row row-cols-1 row-cols-md-2 rows-3">


                        <!-- FEATURE BOX #1 -->
                        <div class="col">
                            <div class="fbox-11 fb-1 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-graphics"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z"
                                                    transform="translate(100 100)"/>
                                            </svg>

                                        </div>
                                    </div>
                                </div>    <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Affordable Prices</h6>
                                    <p>Our CV design service in Ireland is tailored to meet the needs of applicants on a
                                        budget.
                                        We provide high-quality CV writing services at competitive prices, ensuring that
                                        everyone has the opportunity to present their best selves to potential
                                        employers.
                                    </p>
                                </div>

                            </div>
                        </div>    <!-- END FEATURE BOX #1 -->


                        <!-- FEATURE BOX #2 -->
                        <div class="col">
                            <div class="fbox-11 fb-2 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-idea"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z"
                                                    transform="translate(100 100)"/>
                                            </svg>

                                        </div>
                                    </div>
                                </div>    <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Interview Preparation</h6>
                                    <p>We provide straightforward and effective one-on-one interview coaching designed
                                        to elevate your performance in job interviews.
                                    </p>
                                </div>

                            </div>
                        </div>    <!-- END FEATURE BOX #2 -->


                        <!-- FEATURE BOX #3 -->
                        <div class="col">
                            <div class="fbox-11 fb-3 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-graphic"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z"
                                                    transform="translate(100 100)"/>
                                            </svg>

                                        </div>
                                    </div>
                                </div>    <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Elevate Your Career Potential</h6>
                                    <p>Our CV writing and coaching services elevate the quality of your professional
                                        presentation.
                                        By highlighting your unique skills and experiences, we help you stand out in the
                                        competitive IT job market.
                                    </p>
                                </div>

                            </div>
                        </div>    <!-- END FEATURE BOX #3 -->


                        <!-- FEATURE BOX #4 -->
                        <div class="col">
                            <div class="fbox-11 fb-4 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-wireframe"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z"
                                                    transform="translate(100 100)"/>
                                            </svg>

                                        </div>
                                    </div>
                                </div>    <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Linkedin Training</h6>
                                    <p>We create standout LinkedIn profiles and offer essential strategies to enhance
                                        your online visibility.
                                        Let us help you make a lasting impression in the digital professional landscape!
                                    </p>
                                </div>

                            </div>
                        </div>    <!-- END FEATURE BOX #4 -->


                        <!-- FEATURE BOX #5 -->
                        <div class="col">
                            <div class="fbox-11 fb-5 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-trophy"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z"
                                                    transform="translate(100 100)"/>
                                            </svg>

                                        </div>
                                    </div>
                                </div>    <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Executive CV Writing</h6>
                                    <p>In today's competitive job market, standing out is essential!
                                        Leverage our expertise in crafting compelling CVs that capture attention and
                                        highlight your unique strengths.
                                    </p>
                                </div>

                            </div>
                        </div>    <!-- END FEATURE BOX #5 -->


                        <!-- FEATURE BOX #6 -->
                        <div class="col">
                            <div class="fbox-11 fb-6 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-search-engine-1"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z"
                                                    transform="translate(100 100)"/>
                                            </svg>

                                        </div>
                                    </div>
                                </div>    <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt mb-20">
                                    <h6 class="s-22 w-700">Job Search Strategies</h6>
                                    <p>Discover effective techniques to boost your visibility and tap into the hidden
                                        job market.
                                        Master smart strategies to effectively target advertised positions and enhance
                                        your job search success!
                                    </p>
                                </div>

                            </div>
                        </div>    <!-- END FEATURE BOX #6 -->


                    </div>  <!-- End row -->
                </div>    <!-- END FEATURES-11 WRAPPER -->


            </div>     <!-- End container -->
        </section>    <!-- END FEATURES-11 -->

        <!-- DIVIDER LINE -->
        <hr class="divider">

        <!-- TEXT CONTENT
        ============================================= -->
        <section id="lnk-2" class="pt-100 ct-02 content-section division">
            <div class="container">


                <!-- SECTION CONTENT (ROW) -->
                <div class="row d-flex align-items-center">


                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6">
                        <div class="img-block left-column wow fadeInRight">
                            <img class="img-fluid" src="{{ asset('images/img-07.png') }}" alt="content-image"></div>
                    </div>

                    <!-- TEXT BLOCK -->
                    <div class="col-md-6">
                        <div class="txt-block right-column wow fadeInLeft">

                            <!-- Section ID -->
                            <span class="section-id">Strategies That Work</span>

                            <!-- Title -->
                            <h2 class="s-46 w-700">Right strategies & implementations</h2>

                            <!-- Text -->
                            <p>In this program, we equip IT professionals with actionable strategies and proven
                                methodologies for career acceleration.
                                From the identification of growth opportunities through to the implementation of a
                                customized career plan,
                                this program ensures that you are equipped to remain competitive in today's truly fluid
                                tech environment.
                            </p>

                            <!-- Text -->
                            <p class="mb-0">Our approach combines deep industry insights with practical tools to help
                                you achieve your goals effectively and confidently.
                                Let me know if you'd like further refinements!
                            </p>

                            <!-- Link -->
                            <div class="txt-block-tra-link mt-25">
                                <a href="#integrations-1" class="tra-link ico-20 color--theme">
                                    Friendly with others <span class="flaticon-next"></span>
                                </a>
                            </div>

                        </div>
                    </div>    <!-- END TEXT BLOCK -->


                </div>    <!-- END SECTION CONTENT (ROW) -->


            </div>       <!-- End container -->
        </section>    <!-- END TEXT CONTENT -->


        <!-- TEXT CONTENT
        ============================================= -->
        <section class="py-100 ct-01 content-section division">
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
                                <p>Our career guidance progresses with your development. We provide adaptive strategies
                                    and
                                    continuous support to align with evolving needs throughout your IT career journey.
                                    Our solutions cater to every stage-from entry-level to the uppermost senior roles-so
                                    that your ambitions and
                                    aspirations coordinate with any stage of your growth with confidence and awesome
                                    clarity.
                                </p>

                            </div>    <!-- END TEXT BOX -->


                            <!-- TEXT BOX -->
                            <div class="txt-box mb-0">

                                <!-- Title -->
                                <h5 class="s-24 w-700">Connect your data sources</h5>

                                <!-- Text -->
                                <p class="text-justify">Here, we look forward to helping you embark on a cohesive
                                    strategy that assembles skills and
                                    experience and sets goals into pieces that foster success.
                                </p>

                                <!-- List -->
                                <ul class="simple-list">

                                    <li class="list-item">
                                        <p class="text-justify">Identify your strengths as required in the IT industry,
                                            which gives you a clear route.
                                        </p>
                                    </li>

                                    <li class="list-item">
                                        <p class="text-justify mb-2">Make data-informed career choices, skill
                                            acquisition, and growth plans.
                                        </p>
                                    </li>

                                    <p class="text-justify">With our help, you connect your present and your future self
                                        by ensuring that every step taken is efficacious.
                                    </p>

                                </ul>

                            </div>    <!-- END TEXT BOX -->
                        </div>
                    </div>    <!-- END TEXT BLOCK -->

                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6 order-first order-md-2">
                        <div class="img-block right-column wow fadeInLeft">
                            <img class="img-fluid" src="{{ asset('images/img-06.png') }}" alt="content-image"></div>
                    </div>


                </div>    <!-- END SECTION CONTENT (ROW) -->


            </div>       <!-- End container -->
        </section>    <!-- END TEXT CONTENT -->

        <hr class="divider">


        <!-- TEXT CONTENT
        ============================================= -->
        <section class="pt-100 ct-01 content-section division">
            <div class="container">


                <!-- SECTION CONTENT (ROW) -->
                <div class="row d-flex align-items-center">


                    <!-- TEXT BLOCK -->
                    <div class="col-md-6 order-last order-md-2">
                        <div class="txt-block left-column wow fadeInRight">

                            <!-- Section ID -->
                            <span class="section-id color--grey">Productivity Focused</span>

                            <!-- Title -->
                            <h2 class="s-46 w-700">Achieve more with better workflows</h2>

                            <!-- Text -->
                            <p>Transform your professional journey with our cutting-edge workflow solutions.
                                Our innovative training programs are designed to enhance your career progression while
                                providing real-time performance tracking for both you and our expert team.
                            </p>

                            <!-- List -->
                            <ul class="simple-list">

                                <li class="list-item">
                                    <p class="mb-2"><b>Progress Monitoring:</b> Our intuitive workflow system allows you
                                        to visualize your career advancement, tracking milestones and skill acquisitions
                                        in real-time.
                                    </p>
                                </li>

                                <li class="list-item">
                                    <p class="mb-2"><b>Learning Paths:</b> Benefit from customized workflows that adapt
                                        to your unique career goals, ensuring efficient and targeted skill development.
                                    </p>
                                </li>
                                <li class="list-item">
                                    <p class="mb-2"><b>Performance Tracking:</b> Work hand-in-hand with our career
                                        experts, leveraging shared workflows to measure progress, identify areas for
                                        improvement, and celebrate your successes.
                                    </p>
                                </li>
                            </ul>

                            <!-- Link -->
                            <div class="txt-block-tra-link mt-25">
                                <a href="#features-5" class="tra-link ico-20 color--theme">
                                    All-in-one platform <span class="flaticon-next"></span>
                                </a>
                            </div>

                        </div>
                    </div>    <!-- END TEXT BLOCK -->


                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6 order-first order-md-2">
                        <div class="img-block right-column wow fadeInLeft">
                            <img class="img-fluid" src="{{ asset('images/img-05.png') }}" alt="content-image"></div>
                    </div>


                </div>    <!-- END SECTION CONTENT (ROW) -->


            </div>       <!-- End container -->
        </section>    <!-- END TEXT CONTENT -->


        <!-- FEATURES-5
        ============================================= -->
        <section id="features-5" class="pt-100 features-section division">
            <div class="container">

                <!-- FEATURES-5 WRAPPER -->
                <div class="fbox-wrapper text-center">
                    <div class="row d-flex align-items-center">

                    </div>  <!-- End row -->
                </div>    <!-- END FEATURES-5 WRAPPER -->


            </div>     <!-- End container -->
        </section>    <!-- END FEATURES-5 -->

        <hr class="divider">

        <!-- TEXT CONTENT
        ============================================= -->
        <section class="pt-100 ct-02 content-section division">
            <div class="container">


                <!-- SECTION TITLE -->
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-9">
                        <div class="section-title mb-80">

                            <!-- Title -->
                            <h2 class="s-50 w-700">The Complete Solutions</h2>

                            <!-- Text -->
                            <p class="s-21 color--grey">Ligula risus auctor tempus magna feugiat lacinia.</p>

                        </div>
                    </div>
                </div>


                <!-- SECTION CONTENT (ROW) -->
                <div class="row d-flex align-items-center">


                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6">
                        <div class="img-block left-column wow fadeInRight">
                            <img class="img-fluid" src="{{ asset('images/img-03.png') }}" alt="content-image"></div>
                    </div>


                    <!-- TEXT BLOCK -->
                    <div class="col-md-6">
                        <div class="txt-block right-column wow fadeInLeft">


                            <!-- CONTENT BOX #1 -->
                            <div class="cbox-4">

                                <!-- Icon & Title -->
                                <div class="box-title">
                                    <span class="flaticon-paper-sizes color--theme"></span>
                                    <h5 class="s-22 w-700">Built-in automation</h5>
                                </div>

                                <!-- Text -->
                                <div class="cbox-4-txt">
                                    <p>Quaerat sodales sapien blandit purus primis purus ipsum cubilia laoreet augue
                                        luctus and magna dolor luctus egestas an ipsum sapien primis vitae volute and
                                        magna turpis
                                    </p>
                                </div>

                            </div>    <!-- END CONTENT BOX #1 -->


                            <!-- CONTENT BOX #2 -->
                            <div class="cbox-4">

                                <!-- Icon & Title -->
                                <div class="box-title">
                                    <span class="flaticon-layers-1 color--theme"></span>
                                    <h5 class="s-22 w-700">Automatic workflows</h5>
                                </div>

                                <!-- Text -->
                                <div class="cbox-4-txt">
                                    <p>Quaerat sodales sapien blandit purus primis purus ipsum cubilia laoreet augue
                                        luctus and magna dolor luctus egestas an ipsum sapien primis vitae volute and
                                        magna turpis
                                    </p>
                                </div>

                            </div>    <!-- END CONTENT BOX #2 -->


                            <!-- CONTENT BOX #3 -->
                            <div class="cbox-4">

                                <!-- Icon & Title -->
                                <div class="box-title">
                                    <span class="flaticon-pie-chart color--theme"></span>
                                    <h5 class="s-22 w-700">Real-time analytics</h5>
                                </div>

                                <!-- Text -->
                                <div class="cbox-4-txt">
                                    <p class="mb-0">Quaerat sodales sapien blandit purus primis purus ipsum cubilia
                                        laoreet
                                        augue luctus and magna dolor luctus egestas an ipsum sapien primis vitae volute
                                        and
                                        magna turpis
                                    </p>
                                </div>

                            </div>    <!-- END CONTENT BOX #3 -->


                        </div>
                    </div>    <!-- END TEXT BLOCK -->


                </div>    <!-- END SECTION CONTENT (ROW) -->


            </div>       <!-- End container -->
        </section>    <!-- END TEXT CONTENT -->


        <!-- TEXT CONTENT
        ============================================= -->
        <section class="pt-100 ct-03 content-section division">
            <div class="container">
                <div class="row d-flex align-items-center">


                    <!-- TEXT BLOCK -->
                    <div class="col-md-6 col-lg-5 order-last order-md-2">
                        <div class="txt-block left-column wow fadeInRight">

                            <!-- Section ID -->
                            <span class="section-id">Engagement Analytics</span>

                            <!-- Title -->
                            <h2 class="s-46 w-700">Data-driven digital marketing</h2>

                            <!-- List -->
                            <ul class="simple-list">

                                <li class="list-item">
                                    <p>Tempor sapien quaerat undo ipsum laoreet purus and sapien dolor ociis ultrice
                                        ipsum aliquam undo congue dolor cursus congue varius magnis
                                    </p>
                                </li>

                                <li class="list-item">
                                    <p class="mb-0">Cursus purus suscipit vitae cubilia magnis diam volute egestas
                                        sapien ultrice auctor
                                    </p>
                                </li>

                            </ul>

                        </div>
                    </div>    <!-- END TEXT BLOCK -->


                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6 col-lg-7 order-first order-md-2">
                        <div class="img-block right-column wow fadeInLeft">
                            <img class="img-fluid" src="{{ asset('images/img-14.png') }}" alt="content-image"></div>
                    </div>


                </div>    <!-- End row -->
            </div>       <!-- End container -->
        </section>    <!-- END TEXT CONTENT -->



        <!-- TESTIMONIALS-1
        ============================================= -->
        <section id="reviews-1" class="py-100 shape--06 shape--gr-whitesmoke reviews-section mt-20">
            <div class="container">


                <!-- SECTION TITLE -->
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-9">
                        <div class="section-title mb-70">

                            <!-- Title -->
                            <h2 class="s-50 w-700">Our Happy Customers</h2>

                            <!-- Text -->
                            <p class="s-21 color--grey">Ligula risus auctor tempus magna feugiat lacinia.</p>

                        </div>
                    </div>
                </div>


                <!-- TESTIMONIALS CONTENT -->
                <div class="row">
                    <div class="col">
                        <div class="owl-carousel owl-theme reviews-1-wrapper">


                            <!-- TESTIMONIAL #1 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>Just what I was looking for. CODECV did exactly what you said it does.
                                        I would also like to say thank you to all your staff. It was the perfect solution for my career.
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/Luciana.png') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Luciana Machado</h6>
                                            <p class="p-sm">Project Manager</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #1 -->


                            <!-- TESTIMONIAL #2 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>CODECV ensured that the information and skills included were relevant to the IT position I was applying for.
                                        We have optimized my resume with achievements and responsibilities.
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/Lucas.png') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Lucas Verdan</h6>
                                            <p class="p-sm">IT Analyst</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #2 -->


                            <!-- TESTIMONIAL #3 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>Mauris gestas magnis a sapien etiam sapien congue an augue egestas and ultrice
                                        vitae purus diam an integer congue magna ligula egestas magna suscipit
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/review-author-3.jpg') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Marisol19</h6>
                                            <p class="p-sm">Project Manager</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #3 -->


                            <!-- TESTIMONIAL #4 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>Mauris donec a magnis sapien etiam pretium a congue augue volutpat lectus
                                        aenean magna and undo mauris lectus laoreet tempor egestas rutrum
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/review-author-4.jpg') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Leslie D.</h6>
                                            <p class="p-sm">Web Developer</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #4 -->


                            <!-- TESTIMONIAL #5 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>An augue cubilia undo laoreet magna suscipit egestas ipsum lectus purus ipsum
                                        and primis augue an ultrice ligula egestas suscipit a lectus gestas auctor
                                        tempus
                                        feugiat impedit
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/review-author-5.jpg') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Jennifer Harper</h6>
                                            <p class="p-sm">App Developer</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #5 -->


                            <!-- TESTIMONIAL #6 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>An augue cubilia laoreet undo magna ipsum semper suscipit egestas magna ipsum
                                        ligula a vitae purus and ipsum primis cubilia magna suscipit
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/review-author-6.jpg') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Jonathan Barnes</h6>
                                            <p class="p-sm">jQuery Programmer</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #6 -->


                            <!-- TESTIMONIAL #7 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>Augue egestas porta tempus volutpat egestas augue cubilia laoreet a magna
                                        suscipit luctus dolor blandit vitae purus neque tempus an aliquet porta gestas
                                        rutrum blandit vitae
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/review-author-7.jpg') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Mike Harris</h6>
                                            <p class="p-sm">Graphic Designer</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #7 -->


                            <!-- TESTIMONIAL #8 -->
                            <div class="review-1 bg--white-100 block-shadow r-08">

                                <!-- Quote Icon -->
                                <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>

                                <!-- Text -->
                                <div class="review-txt">

                                    <!-- Text -->
                                    <p>Augue at vitae purus tempus egestas volutpat augue undo cubilia laoreet magna
                                        suscipit luctus dolor blandit at purus tempus feugiat impedit
                                    </p>

                                    <!-- Author -->
                                    <div class="author-data clearfix">

                                        <!-- Avatar -->
                                        <div class="review-avatar">
                                            <img src="{{ asset('images/review-author-8.jpg') }}" alt="review-avatar">
                                        </div>

                                        <!-- Data -->
                                        <div class="review-author">
                                            <h6 class="s-18 w-700">Evelyn Martinez</h6>
                                            <p class="p-sm">WordPress Consultant</p>
                                        </div>

                                    </div>    <!-- End Author -->

                                </div>    <!-- End Text -->

                            </div>    <!-- END TESTIMONIAL #8 -->


                        </div>
                    </div>
                </div>    <!-- END TESTIMONIALS CONTENT -->


            </div>       <!-- End container -->
        </section>    <!-- END TESTIMONIALS-1 -->

        <!-- DIVIDER LINE -->
        <hr class="divider">

        <!-- BANNER-7
        ============================================= -->
        <section id="banner-7" class="banner-section">
            <div class="banner-overlay py-100">
                <div class="container">


                    <!-- BANNER-7 WRAPPER -->
                    <div class="banner-7-wrapper">
                        <div class="row justify-content-center">


                            <!-- BANNER-7 TEXT -->
                            <div class="col-md-8">
                                <div class="banner-7-txt text-center">

                                    <!-- Title -->
                                    <h2 class="s-45 w-700">Starting with <span class="color--theme">CODECV</span> is
                                        easy and fast</h2>

                                    <!-- Buttons -->
                                    <div class="btns-group">
                                        <a href="signup-1.html" class="btn r-04 btn--theme hover--theme">Get srarted -
                                            it's free
                                        </a>
                                        <a href="{{ route('faqs') }}" class="btn r-04 btn--tra-black hover--theme">Read
                                            the FAQs</a></div>

                                </div>
                            </div>


                        </div>   <!-- End row -->
                    </div>    <!-- END BANNER-7 WRAPPER -->


                </div>    <!-- End container -->
            </div>     <!-- End banner overlay -->
        </section>    <!-- END BANNER-7 -->


        <!-- DIVIDER LINE -->
        <hr class="divider">
@endsection
