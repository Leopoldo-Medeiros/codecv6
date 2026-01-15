<template>
  <div>
    <section>

      <!-- PRICING-3 -->
      <section id="pricing-3" class="gr--whitesmoke inner-page-hero pb-60 pricing-section">
        <div class="container">


          <!-- SECTION TITLE -->
          <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
              <div class="section-title text-center mb-60">

                <h2 class="s-52 w-700">Simple, Flexible Pricing</h2>

              </div>
            </div>
          </div> <!-- END SECTION TITLE -->


          <!-- PRICING TABLES -->
          <div class="pricing-3-wrapper text-center">
            <div class="row row-cols-1 row-cols-md-3">


              <!-- FREE PLAN -->
              <div class="col">
                <div id="pt-3-1" class="p-table pricing-3-table bg--white-100 block-shadow r-12 wow fadeInUp">

                  <!-- TABLE HEADER -->
                  <div class="pricing-table-header">

                    <!-- Title -->
                    <h4 class="s-32">Basic</h4>

                    <!-- Price -->
                    <div class="price mt-25">
                      <sup class="color--black">€</sup>
                      <span class="color--black">99</span>
                    </div>

                    <!-- Features List -->
                    <ul class="list-unstyled mt-3 text-start">
                      <li class="mb-2"><i class="bi bi-envelope text-success me-2"></i>Cover Letter Writing</li>
                      <li class="mb-2"><i class="bi bi-file-earmark-text text-success me-2"></i>CV Adjustment</li>
                      <li class="mb-2"><i class="bi bi-pencil-square text-success me-2"></i>CV Grammar Review</li>
                      <li class="mb-2"><i class="bi bi-people me-2 icon-green"></i>Referral Networking</li>
                      <li class="mb-2"><i class="bi bi-whatsapp me-2 icon-green"></i>WhatsApp QA</li>
                      <li class="mb-2"><i class="bi bi-calendar me-2 icon-green"></i>Deadline: ~5 Days</li>
                      <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Video Call (30min)</li>
                      <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>LinkedIn Profile</li>
                      <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Interview Preparation</li>
                    </ul>

                  </div> <!-- END TABLE HEADER -->

                  <!-- BUTTON -->
                  <NuxtLink :to="{ path: '/payment', query: { plan: 'Basic', price: 99 } }"
                    class="pt-btn btn btn--theme hover--theme">Buy it</NuxtLink>

                </div>
              </div> <!-- END FREE PLAN -->


              <!-- PLUS PLAN -->
              <div class="col">
                <div id="pt-3-2" class="p-table pricing-3-table bg--white-100 block-shadow r-12 wow fadeInUp">


                  <!-- TABLE HEADER -->
                  <div class="pricing-table-header">

                    <!-- Title -->
                    <h4 class="s-32">Standard</h4>

                    <div class="pricing-table-header">

                      <!-- Price -->
                      <div class="price mt-25">
                        <sup class="color--black">€</sup>
                        <span class="color--black">199</span>
                      </div>

                      <!-- Features List -->
                      <ul class="list-unstyled mt-3 text-start">
                        <li class="mb-2"><i class="bi bi-envelope text-success me-2"></i>Cover
                          Letter Writing
                        </li>
                        <li class="mb-2"><i class="bi bi-file-earmark-text text-success me-2"></i>CV
                          Adjustment
                        </li>
                        <li class="mb-2"><i class="bi bi-pencil-square text-success me-2"></i>CV
                          Grammar Review
                        </li>
                        <li class="mb-2"><i class="bi bi-people me-2 icon-green"></i>Referral
                          Networking
                        </li>
                        <li class="mb-2"><i class="bi bi-whatsapp me-2 icon-green"></i>WhatsApp QA
                        </li>
                        <li class="mb-2"><i class="bi bi-calendar me-2 icon-green"></i>Deadline: ~5
                          Days
                        </li>
                        <li class="mb-2"><i class="bi bi-camera-video text-success me-2"></i>Video
                          Call (30min)
                        </li>
                        <li class="mb-2"><i class="bi bi-linkedin text-success me-2"></i>LinkedIn
                          Profile
                        </li>
                        <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Interview
                          Preparation
                        </li>
                      </ul>

                    </div> <!-- END TABLE HEADER -->

                  </div> <!-- END TABLE HEADER -->


                  <!-- BUTTON -->
                  <NuxtLink :to="{ path: '/payment', query: { plan: 'Standard', price: 199 } }"
                    class="pt-btn btn btn--theme hover--theme">Buy it</NuxtLink>

                </div>
              </div> <!-- END PLUS PLAN -->


              <!-- PRO PLAN -->
              <div class="col">
                <div id="pt-3-3" class="p-table pricing-3-table bg--white-100 block-shadow r-12 wow fadeInUp">


                  <!-- TABLE HEADER -->
                  <div class="pricing-table-header">

                    <!-- Title -->
                    <h4 class="s-32">Premium</h4>

                    <div class="pricing-table-header">

                      <!-- Price -->
                      <div class="price mt-25">
                        <sup class="color--black">€</sup>
                        <span class="color--black">249</span>
                      </div>

                      <!-- Features List -->
                      <ul class="list-unstyled mt-3 text-start">
                        <li class="mb-2"><i class="bi bi-envelope text-success me-2"></i>Cover
                          Letter Writing
                        </li>
                        <li class="mb-2"><i class="bi bi-file-earmark-text text-success me-2"></i>CV
                          Adjustment
                        </li>
                        <li class="mb-2"><i class="bi bi-pencil-square text-success me-2"></i>CV
                          Grammar Review
                        </li>
                        <li class="mb-2"><i class="bi bi-people me-2 icon-green"></i>Referral
                          Networking
                        </li>
                        <li class="mb-2"><i class="bi bi-whatsapp me-2 icon-green"></i>WhatsApp QA
                        </li>
                        <li class="mb-2"><i class="bi bi-calendar me-2 icon-green"></i>Deadline: ~10
                          Days
                        </li>
                        <li class="mb-2"><i class="bi bi-camera-video text-success me-2"></i>Video
                          Call (30min)
                        </li>
                        <li class="mb-2"><i class="bi bi-linkedin text-success me-2"></i>LinkedIn
                          Profile
                        </li>
                        <li class="mb-2"><i class="bi bi-people-fill text-success me-2"></i>Interview
                          Preparation
                        </li>
                      </ul>
                    </div> <!-- END TABLE HEADER -->

                  </div> <!-- END TABLE HEADER -->

                  <!-- BUTTON -->
                  <NuxtLink :to="{ path: '/payment', query: { plan: 'Premium', price: 249 } }"
                    class="pt-btn btn btn--theme hover--theme">Buy it</NuxtLink>

                </div>
              </div> <!-- END PRO PLAN -->

            </div>
          </div> <!-- PRICING TABLES -->

        </div> <!-- End container -->
      </section> <!-- END PRICING-3 -->
    </section>

    <!-- DIVIDER LINE -->
    <hr class="divider">

    <!-- PRICING COMPARE -->
    <section id="comp-table" class="pt-100 pb-60 pricing-section division">
      <div class="container">


        <!-- SECTION TITLE -->
        <div class="row justify-content-center">
          <div class="col-md-10 col-lg-9">
            <div class="section-title mb-70">

              <!-- Title -->
              <h2 class="s-52 w-700">Compare Our Plans</h2>

              <!-- Text -->
              <p class="p-xl">Complete list of features available in our pricing plans</p>

            </div>
          </div>
        </div>

        <!-- PRICING COMPARE -->
        <div class="comp-table wow fadeInUp">
          <div class="row">
            <div class="col">

              <!-- Table -->
              <div class="table-responsive mb-50">
                <table class="table text-center">

                  <thead>
                    <tr>
                      <th style="width: 34%;"></th>
                      <th style="width: 22%;">Basic</th>
                      <th style="width: 22%;">Standard</th>
                      <th style="width: 22%;">Premium</th>
                    </tr>
                  </thead>

                  <tbody>

                    <tr>
                      <th scope="row" class="text-start">CV writing in EN or PT</th>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                    <tr>
                      <th scope="row" class="text-start">CV Adjustment</th>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                    <tr>
                      <th scope="row" class="text-start">CV Grammar Review</th>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                    <tr>
                      <th scope="row" class="text-start">Referral Networking</th>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                    <tr>
                      <th scope="row" class="text-start">WhatsApp QA</th>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                    <tr>
                      <th scope="row" class="text-start">Deadline: ~5 Days</th>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                    <tr>
                      <th scope="row" class="text-start">LinkedIn Profile</th>
                      <td class="ico-15 disabled-option"><span class="flaticon-cancel text-danger"></span>
                      </td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                    <tr>
                      <th scope="row" class="text-start">Interview Preparation</th>
                      <td class="ico-15 disabled-option"><span class="flaticon-cancel text-danger"></span>
                      </td>
                      <td class="ico-15 disabled-option"><span class="flaticon-cancel text-danger"></span>
                      </td>
                      <td class="ico-20 color--theme"><span class="flaticon-check text-success me-2"></span></td>
                    </tr>

                  </tbody>

                </table>
              </div> <!-- End Table -->

            </div>
          </div>
        </div> <!-- END PRICING COMPARE -->


        <!-- PRICING COMPARE PAYMENT -->
        <div class="comp-table-payment">
          <div class="row row-cols-1 row-cols-md-3">


            <!-- Payment Methods -->
            <div class="col col-lg-5">
              <div id="pbox-1" class="pbox mb-40 wow fadeInUp">

                <!-- Title -->
                <h6 class="s-18 w-700">Accepted Payment Methods</h6>

                <!-- Payment Icons -->
                <ul class="payment-icons ico-45 mt-25">
                  <li><img src="/images/png_icons/visa.png" alt="payment-icon"></li>
                  <li><img src="/images/png_icons/paypal.png" alt="payment-icon"></li>
                  <li><img src="/images/png_icons/revolut.png" alt="payment-icon"></li>
                </ul>

              </div>
            </div>
          </div>
        </div> <!-- END PRICING COMPARE PAYMENT -->


      </div> <!-- End container -->
    </section> <!-- END PRICING COMPARE -->
    <!-- FAQs-3 -->
    <section id="faqs-3" class="gr--whitesmoke pt-100 faqs-section">
      <div class="container">


        <!-- SECTION TITLE -->
        <div class="row justify-content-center">
          <div class="col-md-10 col-lg-9">
            <div class="section-title mb-70">

              <!-- Title -->
              <h2 class="s-52 w-700">Questions & Answers</h2>

            </div>
          </div>
        </div>


        <!-- FAQs-3 QUESTIONS -->
        <div class="faqs-3-questions">
          <div class="row">


            <!-- QUESTIONS HOLDER -->
            <div class="col-lg-6">
              <div class="questions-holder">


                <!-- QUESTION #1 -->
                <div class="question mb-35 wow fadeInUp">

                  <!-- Question -->
                  <h5 class="s-22 w-700"><span>1.</span> Getting started with CODECV</h5>

                  <!-- Answer -->
                  <p class="color--grey">CODECV is a CV career consultancy company that helps IT
                    professionals to create compelling CVs that will stand out from the crowd and land
                    them more job interviews.
                    Our team of expert consultants has a proven track record of success in helping IT
                    professionals achieve their career goals, and we are committed to providing you with
                    the best possible service.
                  </p>

                </div>

                <!-- QUESTION #2 -->
                <div class="question mb-35 wow fadeInUp">

                  <!-- Question -->
                  <h5 class="s-22 w-700"><span>2.</span> Can I make an online appointment?</h5>

                  <!-- Answer -->
                  <p class="color--grey">All appointments could be done via WhatsApp, Instagram's DM or
                    email.</p>

                </div>

                <!-- QUESTION #3 -->
                <div class="question mb-35 wow fadeInUp">

                  <!-- Question -->
                  <h5 class="s-22 w-700"><span>3.</span> How can I select the best plan?</h5>

                  <!-- Answer -->
                  <p class="color--grey">The first step is to book an appointment with CODE CV and explain
                    to us your background.</p>

                </div>


              </div>
            </div> <!-- END QUESTIONS HOLDER -->


            <!-- QUESTIONS WRAPPER -->
            <div class="col-lg-6">
              <div class="questions-holder">


                <!-- QUESTION #4 -->
                <div class="question mb-35 wow fadeInUp">

                  <!-- Question -->
                  <h5 class="s-22 w-700"><span>4.</span> What if I have any questions after the service?
                  </h5>

                  <!-- Answer -->
                  <p class="color--grey">Absolutely fine. Our support is 24/7 pre-sale, and post-sale.</p>

                </div>


                <!-- QUESTION #5 -->
                <div class="question mb-35 wow fadeInUp">

                  <!-- Question -->
                  <h5 class="s-22 w-700"><span>5.</span> How can I pay for the service?</h5>

                  <!-- Answer -->
                  <p class="color--grey">The payment is done via PayPal or Revolut. The bank account
                    details is sent by email.
                  </p>

                </div>


                <!-- QUESTION #6 -->
                <div class="question mb-35 wow fadeInUp">

                  <!-- Question -->
                  <h5 class="s-22 w-700"><span>6.</span> How long does the consultancy take?</h5>

                  <!-- Answer -->
                  <p class="color--grey">The consultancy is provide for Premium Service until the
                    professional pass in the probation period
                  </p>

                </div>


              </div>
            </div> <!-- END QUESTIONS HOLDER -->


          </div> <!-- End row -->
        </div> <!-- END FAQs-3 QUESTIONS -->

      </div> <!-- End container -->
    </section> <!-- END FAQs-3 -->

    <!-- BANNER-1 -->
    <section id="banner-1" class="pt-100 banner-section">
      <div class="container">

      </div> <!-- End container -->
    </section> <!-- END BANNER-1 -->
  </div>
</template>

<script setup>
useHead({
  title: 'Pricing'
})
</script>
