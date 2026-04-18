<template>
  <NuxtLayout name="marketing">
    <!-- HERO-5 -->
      <section id="hero-5" class="bg--scroll hero-section">
        <div class="container">
          <div class="row d-flex align-items-center">

            <div class="col-md-6">
              <div class="hero-5-txt wow fadeInRight">
                <h2 class="s-58 w-700">The growth accelerator for your IT career</h2>
                <p class="p-lg">
                  Expert coaching, curated learning paths, and real job opportunities — everything you need to land your next IT role in Ireland.
                </p>
                <NuxtLink to="/login" class="btn r-04 btn--theme hover--theme">Get started for free</NuxtLink>
              </div>
            </div>

            <div class="col-md-6">
              <div class="hero-5-img wow fadeInLeft">
                <img class="img-fluid" src="/images/img-18.png" alt="hero-image">
              </div>
            </div>

          </div>
        </div>

        <div class="wave-shape-bottom">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 170">
            <path fill-opacity="1" d="M0,160L120,160C240,160,480,160,720,138.7C960,117,1200,75,1320,53.3L1440,32L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z"></path>
          </svg>
        </div>
      </section>


      <!-- STATISTIC -->
      <div id="statistic-1" class="py-100 statistic-section division">
        <div class="container">
          <div class="statistic-1-wrapper">
            <div class="row justify-content-md-center row-cols-1 row-cols-md-3">

              <div class="col">
                <div class="wow fadeInUp">
                  <div class="statistic-block">
                    <div class="statistic-block-digit text-center">
                      <h2 class="s-46 statistic-number"><span class="count-element">200</span>+</h2>
                    </div>
                    <div class="statistic-block-txt color--grey">
                      <p class="p-md">IT professionals coached and placed</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col">
                <div class="wow fadeInUp">
                  <div class="statistic-block">
                    <div class="statistic-block-digit text-center">
                      <h2 class="s-46 statistic-number"><span class="count-element">92</span>%</h2>
                    </div>
                    <div class="statistic-block-txt color--grey">
                      <p class="p-md">Job placement success rate</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col">
                <div class="wow fadeInUp">
                  <div class="statistic-block">
                    <div class="statistic-block-digit text-center">
                      <h2 class="s-46 statistic-number">
                        <span class="count-element">4</span>.<span class="count-element">9</span>
                      </h2>
                    </div>
                    <div class="statistic-block-txt color--grey">
                      <p class="p-md">Average client satisfaction rating</p>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>


      <hr class="divider">


      <!-- FEATURES -->
      <section id="features-11" class="pt-100 features-section division">
        <div class="container">

          <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
              <div class="section-title mb-70">
                <h2 class="s-50 w-700">Everything you need to advance your IT career</h2>
                <p class="s-21 color--grey">From CV writing to job placement — we've got you covered.</p>
              </div>
            </div>
          </div>

          <div class="row row-cols-1 row-cols-md-2 g-4 pb-4">

            <div class="col wow fadeInUp">
              <div style="display:flex;align-items:flex-start;gap:20px;padding:28px 24px;border:1px solid #eef0f7;border-radius:12px;background:#fff;">
                <div style="flex-shrink:0;width:56px;height:56px;border-radius:14px;background:#eef0ff;display:flex;align-items:center;justify-content:center;">
                  <UIcon name="i-heroicons-document-text" style="width:26px;height:26px;color:#4f46e5;" />
                </div>
                <div>
                  <h6 class="s-22 w-700" style="margin-bottom:8px;">Professional CV Writing</h6>
                  <p style="margin:0;">Bespoke CV writing tailored for the Irish IT market. Our experts know what hiring managers look for and craft your story to stand out.</p>
                </div>
              </div>
            </div>

            <div class="col wow fadeInUp">
              <div style="display:flex;align-items:flex-start;gap:20px;padding:28px 24px;border:1px solid #eef0f7;border-radius:12px;background:#fff;">
                <div style="flex-shrink:0;width:56px;height:56px;border-radius:14px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;">
                  <UIcon name="i-heroicons-map" style="width:26px;height:26px;color:#059669;" />
                </div>
                <div>
                  <h6 class="s-22 w-700" style="margin-bottom:8px;">Structured Learning Paths</h6>
                  <p style="margin:0;">Follow curated roadmaps tailored to your target role — from junior dev to cloud architect, with real courses and milestones.</p>
                </div>
              </div>
            </div>

            <div class="col wow fadeInUp">
              <div style="display:flex;align-items:flex-start;gap:20px;padding:28px 24px;border:1px solid #eef0f7;border-radius:12px;background:#fff;">
                <div style="flex-shrink:0;width:56px;height:56px;border-radius:14px;background:#fdf4ff;display:flex;align-items:center;justify-content:center;">
                  <UIcon name="i-heroicons-user-group" style="width:26px;height:26px;color:#9333ea;" />
                </div>
                <div>
                  <h6 class="s-22 w-700" style="margin-bottom:8px;">1-on-1 Expert Coaching</h6>
                  <p style="margin:0;">Work with seasoned IT consultants who know the Irish job market inside out. Get personalised guidance and interview preparation.</p>
                </div>
              </div>
            </div>

            <div class="col wow fadeInUp">
              <div style="display:flex;align-items:flex-start;gap:20px;padding:28px 24px;border:1px solid #eef0f7;border-radius:12px;background:#fff;">
                <div style="flex-shrink:0;width:56px;height:56px;border-radius:14px;background:#fff7ed;display:flex;align-items:center;justify-content:center;">
                  <UIcon name="i-heroicons-briefcase" style="width:26px;height:26px;color:#ea580c;" />
                </div>
                <div>
                  <h6 class="s-22 w-700" style="margin-bottom:8px;">Exclusive Job Listings</h6>
                  <p style="margin:0;">Access curated IT job opportunities matched to your skills and experience. Direct connections to top companies in Dublin and beyond.</p>
                </div>
              </div>
            </div>

            <div class="col wow fadeInUp">
              <div style="display:flex;align-items:flex-start;gap:20px;padding:28px 24px;border:1px solid #eef0f7;border-radius:12px;background:#fff;">
                <div style="flex-shrink:0;width:56px;height:56px;border-radius:14px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                  <UIcon name="i-heroicons-magnifying-glass" style="width:26px;height:26px;color:#2563eb;" />
                </div>
                <div>
                  <h6 class="s-22 w-700" style="margin-bottom:8px;">LinkedIn & ATS Optimisation</h6>
                  <p style="margin:0;">Get your LinkedIn profile and CV optimised for applicant tracking systems. Be found by the right recruiters at the right time.</p>
                </div>
              </div>
            </div>

            <div class="col wow fadeInUp">
              <div style="display:flex;align-items:flex-start;gap:20px;padding:28px 24px;border:1px solid #eef0f7;border-radius:12px;background:#fff;">
                <div style="flex-shrink:0;width:56px;height:56px;border-radius:14px;background:#fefce8;display:flex;align-items:center;justify-content:center;">
                  <UIcon name="i-heroicons-chart-bar" style="width:26px;height:26px;color:#ca8a04;" />
                </div>
                <div>
                  <h6 class="s-22 w-700" style="margin-bottom:8px;">Progress Tracking Dashboard</h6>
                  <p style="margin:0;">Monitor your learning progress, completed courses, and application activity — all in one place with your personal dashboard.</p>
                </div>
              </div>
            </div>

          </div>

        </div>
      </section>


      <!-- TEXT CONTENT - How it works -->
      <section id="lnk-2" class="pt-100 ct-02 content-section division">
        <div class="container">
          <div class="row d-flex align-items-center">

            <div class="col-md-6">
              <div class="img-block left-column wow fadeInRight">
                <img class="img-fluid" src="/images/img-07.png" alt="content-image">
              </div>
            </div>

            <div class="col-md-6">
              <div class="txt-block right-column wow fadeInLeft">
                <span class="section-id">How It Works</span>
                <h2 class="s-46 w-700">Your career accelerator in three steps</h2>
                <p>We pair you with an expert consultant, build your personalised plan, then support you all the way to your next job offer.</p>
                <ul class="simple-list">
                  <li class="list-item">
                    <p><strong>1. Create your profile</strong> — Tell us where you are in your career and where you want to go.</p>
                  </li>
                  <li class="list-item">
                    <p><strong>2. Get matched with a consultant</strong> — Your dedicated coach builds your learning path and CV strategy.</p>
                  </li>
                  <li class="list-item">
                    <p class="mb-0"><strong>3. Land the job</strong> — Follow your path, apply to curated roles, and get hired.</p>
                  </li>
                </ul>
              </div>
            </div>

          </div>
        </div>
      </section>


      <!-- FEATURES-2 -->
      <section class="py-100 features-section division">
        <div class="container">

          <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
              <div class="section-title mb-80">
                <h2 class="s-50 w-700">Everything in One Platform</h2>
                <p class="s-21 color--grey">Manage your entire career journey from a single dashboard.</p>
              </div>
            </div>
          </div>

          <div class="fbox-wrapper text-center">
            <div class="row row-cols-1 row-cols-md-3">

              <div class="col">
                <div class="fbox-2 fb-1 wow fadeInUp">
                  <div class="fbox-img gr--whitesmoke h-175">
                    <img class="img-fluid" src="/images/f_01.png" alt="feature-image">
                  </div>
                  <div class="fbox-txt">
                    <h6 class="s-22 w-700">Personal Dashboard</h6>
                    <p>Track your courses, job applications, and coaching sessions in one place.</p>
                  </div>
                </div>
              </div>

              <div class="col">
                <div class="fbox-2 fb-2 wow fadeInUp">
                  <div class="fbox-img gr--whitesmoke h-175">
                    <img class="img-fluid" src="/images/f_05.png" alt="feature-image">
                  </div>
                  <div class="fbox-txt">
                    <h6 class="s-22 w-700">Learning Paths</h6>
                    <p>Curated course sequences designed for your specific target role.</p>
                  </div>
                </div>
              </div>

              <div class="col">
                <div class="fbox-2 fb-3 wow fadeInUp">
                  <div class="fbox-img gr--whitesmoke h-175">
                    <img class="img-fluid" src="/images/f_02.png" alt="feature-image">
                  </div>
                  <div class="fbox-txt">
                    <h6 class="s-22 w-700">Job Board</h6>
                    <p>Exclusive IT job listings matched to your skills and career stage.</p>
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </section>


      <hr class="divider">


      <!-- TESTIMONIALS -->
      <section id="reviews-1" class="py-100 shape--06 shape--gr-whitesmoke reviews-section">
        <div class="container">

          <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
              <div class="section-title mb-70">
                <h2 class="s-50 w-700">What Our Clients Say</h2>
                <p class="s-21 color--grey">Real stories from IT professionals who've transformed their careers.</p>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="owl-carousel owl-theme reviews-1-wrapper">

                <div class="review-1 bg--white-100 block-shadow r-08">
                  <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
                  <div class="review-txt">
                    <p>CODECV helped me land a senior developer role in Dublin within 3 months. The CV rewrite and coaching sessions were game-changing.</p>
                    <div class="author-data clearfix">
                      <div class="review-avatar"><img src="/images/review-author-1.jpg" alt="review"></div>
                      <div class="review-author">
                        <h6 class="s-18 w-700">Tomasz K.</h6>
                        <p class="p-sm">Senior Java Developer</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="review-1 bg--white-100 block-shadow r-08">
                  <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
                  <div class="review-txt">
                    <p>The structured learning path gave me a clear roadmap. I went from junior to mid-level engineer in 6 months following the programme.</p>
                    <div class="author-data clearfix">
                      <div class="review-avatar"><img src="/images/review-author-2.jpg" alt="review"></div>
                      <div class="review-author">
                        <h6 class="s-18 w-700">Maria S.</h6>
                        <p class="p-sm">Software Engineer</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="review-1 bg--white-100 block-shadow r-08">
                  <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
                  <div class="review-txt">
                    <p>Incredible support throughout. My consultant knew exactly which companies were hiring and helped me tailor my applications. Highly recommend.</p>
                    <div class="author-data clearfix">
                      <div class="review-avatar"><img src="/images/review-author-3.jpg" alt="review"></div>
                      <div class="review-author">
                        <h6 class="s-18 w-700">Arun P.</h6>
                        <p class="p-sm">Cloud Engineer</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="review-1 bg--white-100 block-shadow r-08">
                  <div class="review-ico ico-65"><span class="flaticon-quote"></span></div>
                  <div class="review-txt">
                    <p>I was stuck in the same role for 3 years. CODECV's coaching and the job board helped me move into a tech lead position within 4 months.</p>
                    <div class="author-data clearfix">
                      <div class="review-avatar"><img src="/images/review-author-4.jpg" alt="review"></div>
                      <div class="review-author">
                        <h6 class="s-18 w-700">Ciara M.</h6>
                        <p class="p-sm">Tech Lead</p>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
      </section>


      <hr class="divider">


      <!-- BANNER CTA -->
      <section id="banner-7" class="banner-section">
        <div class="banner-overlay py-100">
          <div class="container">
            <div class="banner-7-wrapper">
              <div class="row justify-content-center">
                <div class="col-md-8">
                  <div class="banner-7-txt text-center">
                    <h2 class="s-50 w-700">Start accelerating your IT career today</h2>
                    <div class="btns-group">
                      <NuxtLink to="/login" class="btn r-04 btn--theme hover--theme">Get started — it's free</NuxtLink>
                      <NuxtLink to="/pricing" class="btn r-04 btn--tra-white hover--theme">View pricing</NuxtLink>
                    </div>
                    <p class="btn-txt ico-15">No credit card required to get started.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>


      <hr class="divider">


      </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

useHead({ title: 'CODECV — IT Career Coaching Platform' })

const { isAuthenticated, logout } = useAuth()
</script>
