<template>
  <NuxtLayout name="marketing">
    <div>

      <!-- PRICING-3 -->
      <section id="pricing-3" class="gr--whitesmoke inner-page-hero pb-60 pricing-section">
        <div class="container">

          <!-- SECTION TITLE -->
          <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
              <div class="section-title text-center mb-60">
                <h2 class="s-52 w-700">Simple, Flexible Pricing</h2>
                <p class="p-xl color--grey">Choose the plan that fits your career goals — from self-paced CV services to full bootcamp training.</p>
              </div>
            </div>
          </div>

          <!-- PRICING TABLES -->
          <div class="row row-cols-1 row-cols-md-3 g-4 align-items-stretch wow fadeInUp">

            <!-- CAREER ACCELERATOR -->
            <div class="col d-flex">
              <div style="display:flex;flex-direction:column;width:100%;background:#fff;border:1px solid #e8eaf0;border-radius:16px;padding:36px 32px;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
                <p style="font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#6366f1;margin-bottom:12px;">Starter</p>
                <h4 style="font-size:22px;font-weight:700;color:#1a1a2e;margin-bottom:4px;">Career Accelerator</h4>
                <p style="font-size:14px;color:#6b7280;margin-bottom:24px;">One-time, self-serve delivery</p>
                <div style="margin-bottom:8px;">
                  <span style="font-size:48px;font-weight:800;color:#1a1a2e;line-height:1;">€99</span>
                </div>
                <p style="font-size:13px;color:#9ca3af;margin-bottom:28px;">one-time payment</p>
                <div style="flex:1;">
                  <div v-for="item in acceleratorFeatures" :key="item.text"
                    style="display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid #f3f4f6;">
                    <span :style="`flex-shrink:0;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;background:${item.ok ? '#dcfce7' : '#fee2e2'};color:${item.ok ? '#16a34a' : '#dc2626'};`">
                      {{ item.ok ? '✓' : '✗' }}
                    </span>
                    <span style="font-size:14px;color:#374151;">{{ item.text }}</span>
                  </div>
                </div>
                <a href="https://wa.me/353894050730?text=Hi%2C+I%27m+interested+in+the+Career+Accelerator+%28%E2%82%AC99%29.+Can+you+tell+me+more%3F"
                  target="_blank"
                  style="display:block;margin-top:32px;padding:14px;text-align:center;border-radius:10px;font-size:15px;font-weight:600;color:#4f46e5;border:2px solid #4f46e5;text-decoration:none;transition:all .2s;"
                  onmouseover="this.style.background='#4f46e5';this.style.color='#fff'"
                  onmouseout="this.style.background='transparent';this.style.color='#4f46e5'">
                  Get Started
                </a>
              </div>
            </div>

            <!-- BOOTCAMP — featured -->
            <div class="col d-flex">
              <div style="display:flex;flex-direction:column;width:100%;background:linear-gradient(160deg,#4f46e5,#7c3aed);border-radius:16px;padding:36px 32px;box-shadow:0 8px 40px rgba(79,70,229,0.35);position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;right:0;background:#fbbf24;color:#1a1a2e;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:6px 16px;border-radius:0 16px 0 12px;">
                  Best Value
                </div>
                <p style="font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#a5b4fc;margin-bottom:12px;">Bootcamp</p>
                <h4 style="font-size:22px;font-weight:700;color:#fff;margin-bottom:4px;">Laravel + NR Bootcamp</h4>
                <p style="font-size:14px;color:#c7d2fe;margin-bottom:24px;">Cohort-based, 12–16 weeks</p>
                <div style="margin-bottom:8px;">
                  <span style="font-size:48px;font-weight:800;color:#fff;line-height:1;">€1,500</span>
                </div>
                <p style="font-size:13px;color:#a5b4fc;margin-bottom:28px;">per student</p>
                <div style="flex:1;">
                  <div v-for="item in bootcampFeatures" :key="item.text"
                    style="display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,0.1);">
                    <span :style="`flex-shrink:0;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;background:${item.ok ? 'rgba(255,255,255,0.2)' : 'rgba(255,255,255,0.08)'};color:${item.ok ? '#fff' : '#a5b4fc'};`">
                      {{ item.ok ? '✓' : '✗' }}
                    </span>
                    <span style="font-size:14px;color:#e0e7ff;">{{ item.text }}</span>
                  </div>
                </div>
                <a href="https://wa.me/353894050730?text=Hi%2C+I%27m+interested+in+the+Laravel+%2B+NR+Bootcamp+%28%E2%82%AC1%2C500%29.+Can+you+tell+me+more%3F"
                  target="_blank"
                  style="display:block;margin-top:32px;padding:14px;text-align:center;border-radius:10px;font-size:15px;font-weight:600;color:#4f46e5;background:#fff;text-decoration:none;transition:opacity .2s;"
                  onmouseover="this.style.opacity='.9'"
                  onmouseout="this.style.opacity='1'">
                  Enroll Now
                </a>
              </div>
            </div>

            <!-- MENTORSHIP -->
            <div class="col d-flex">
              <div style="display:flex;flex-direction:column;width:100%;background:#fff;border:1px solid #e8eaf0;border-radius:16px;padding:36px 32px;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
                <p style="font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#6366f1;margin-bottom:12px;">Premium</p>
                <h4 style="font-size:22px;font-weight:700;color:#1a1a2e;margin-bottom:4px;">1-on-1 Mentorship</h4>
                <p style="font-size:14px;color:#6b7280;margin-bottom:24px;">Ongoing, personalised support</p>
                <div style="margin-bottom:8px;">
                  <span style="font-size:48px;font-weight:800;color:#1a1a2e;line-height:1;">€249</span>
                </div>
                <p style="font-size:13px;color:#9ca3af;margin-bottom:28px;">per month · cancel anytime</p>
                <div style="flex:1;">
                  <div v-for="item in mentorshipFeatures" :key="item.text"
                    style="display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="flex-shrink:0;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;background:#dcfce7;color:#16a34a;">✓</span>
                    <span style="font-size:14px;color:#374151;">{{ item }}</span>
                  </div>
                </div>
                <a href="https://wa.me/353894050730?text=Hi%2C+I%27m+interested+in+the+1-on-1+Mentorship+%28%E2%82%AC249%2Fmonth%29.+Can+we+book+a+discovery+call%3F"
                  target="_blank"
                  style="display:block;margin-top:32px;padding:14px;text-align:center;border-radius:10px;font-size:15px;font-weight:600;color:#4f46e5;border:2px solid #4f46e5;text-decoration:none;transition:all .2s;"
                  onmouseover="this.style.background='#4f46e5';this.style.color='#fff'"
                  onmouseout="this.style.background='transparent';this.style.color='#4f46e5'">
                  Book a Call
                </a>
              </div>
            </div>

          </div>

        </div>
      </section>

      <!-- DIVIDER LINE -->
      <hr class="divider">

      <!-- PRICING COMPARE -->
      <section id="comp-table" class="pt-100 pb-60 pricing-section division">
        <div class="container">

          <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
              <div class="section-title mb-70">
                <h2 class="s-52 w-700">Compare Plans</h2>
                <p class="p-xl">Full feature breakdown across all pricing tiers</p>
              </div>
            </div>
          </div>

          <!-- Modern compare table -->
          <div class="wow fadeInUp" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:separate;border-spacing:0;border-radius:16px;overflow:hidden;box-shadow:0 4px 32px rgba(0,0,0,0.08);">
              <!-- Header -->
              <thead>
                <tr>
                  <th style="width:34%;padding:20px 24px;background:#f8f9ff;text-align:left;font-size:13px;color:#6b7280;font-weight:600;border-bottom:1px solid #e8eaf0;">Feature</th>
                  <th style="width:22%;padding:20px 16px;background:#f8f9ff;text-align:center;border-bottom:1px solid #e8eaf0;">
                    <div style="font-size:13px;font-weight:700;color:#1a1a2e;">Career Accelerator</div>
                    <div style="font-size:12px;color:#9ca3af;margin-top:2px;">€99 one-time</div>
                  </th>
                  <th style="width:22%;padding:20px 16px;background:linear-gradient(135deg,#4f46e5,#7c3aed);text-align:center;border-bottom:1px solid rgba(255,255,255,0.15);position:relative;">
                    <div style="font-size:13px;font-weight:700;color:#fff;">Bootcamp</div>
                    <div style="font-size:12px;color:#c7d2fe;margin-top:2px;">€1,500 one-time</div>
                  </th>
                  <th style="width:22%;padding:20px 16px;background:#f8f9ff;text-align:center;border-bottom:1px solid #e8eaf0;">
                    <div style="font-size:13px;font-weight:700;color:#1a1a2e;">Mentorship</div>
                    <div style="font-size:12px;color:#9ca3af;margin-top:2px;">€249/month</div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, i) in compareRows" :key="row.feature"
                  :style="`background:${i % 2 === 0 ? '#fff' : '#fafafe'};`">
                  <td style="padding:14px 24px;font-size:14px;color:#374151;font-weight:500;border-bottom:1px solid #f0f0f7;">
                    {{ row.feature }}
                  </td>
                  <td style="padding:14px 16px;text-align:center;border-bottom:1px solid #f0f0f7;">
                    <span v-if="row.accelerator"
                      style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#dcfce7;color:#16a34a;font-size:12px;font-weight:700;">✓</span>
                    <span v-else
                      style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#f3f4f6;color:#9ca3af;font-size:11px;">✗</span>
                  </td>
                  <td style="padding:14px 16px;text-align:center;background:rgba(79,70,229,0.04);border-bottom:1px solid rgba(79,70,229,0.08);">
                    <span v-if="row.bootcamp"
                      style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#e0e7ff;color:#4f46e5;font-size:12px;font-weight:700;">✓</span>
                    <span v-else
                      style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#f3f4f6;color:#9ca3af;font-size:11px;">✗</span>
                  </td>
                  <td style="padding:14px 16px;text-align:center;border-bottom:1px solid #f0f0f7;">
                    <span v-if="row.mentorship"
                      style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#dcfce7;color:#16a34a;font-size:12px;font-weight:700;">✓</span>
                    <span v-else
                      style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:#f3f4f6;color:#9ca3af;font-size:11px;">✗</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </section>

      <!-- DIVIDER LINE -->
      <hr class="divider">

      <!-- FAQs -->
      <section id="faqs-3" class="gr--whitesmoke pt-100 pb-60 faqs-section">
        <div class="container">

          <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
              <div class="section-title mb-70">
                <h2 class="s-52 w-700">Questions &amp; Answers</h2>
              </div>
            </div>
          </div>

          <div class="faqs-3-questions">
            <div class="row">

              <div class="col-lg-6">
                <div class="questions-holder">

                  <div class="question mb-35 wow fadeInUp">
                    <h5 class="s-22 w-700"><span>1.</span> What is included in the Career Accelerator?</h5>
                    <p class="color--grey">The Career Accelerator delivers a complete CV rewrite in English and/or Portuguese, LinkedIn profile optimization, and a tailored cover letter — everything you need to start landing more interviews.</p>
                  </div>

                  <div class="question mb-35 wow fadeInUp">
                    <h5 class="s-22 w-700"><span>2.</span> Who is the Bootcamp for?</h5>
                    <p class="color--grey">The Laravel + New Relic Bootcamp is designed for junior to mid-level developers who want to break into production-level engineering. You'll learn real Laravel architecture and New Relic observability — skills that get you hired.</p>
                  </div>

                  <div class="question mb-35 wow fadeInUp">
                    <h5 class="s-22 w-700"><span>3.</span> How do the bi-weekly sessions work?</h5>
                    <p class="color--grey">Mentorship sessions are 60-minute video calls twice a month. Between sessions you get WhatsApp support and access to all recorded Bootcamp materials.</p>
                  </div>

                </div>
              </div>

              <div class="col-lg-6">
                <div class="questions-holder">

                  <div class="question mb-35 wow fadeInUp">
                    <h5 class="s-22 w-700"><span>4.</span> How do I pay?</h5>
                    <p class="color--grey">Click any plan button to start a WhatsApp conversation. We'll send you payment details and get you set up within 24 hours.</p>
                  </div>

                  <div class="question mb-35 wow fadeInUp">
                    <h5 class="s-22 w-700"><span>5.</span> Can I get a refund?</h5>
                    <p class="color--grey">Career Accelerator and Bootcamp enrollment are non-refundable once work has started. Mentorship plans can be cancelled at any time before the next billing cycle.</p>
                  </div>

                  <div class="question mb-35 wow fadeInUp">
                    <h5 class="s-22 w-700"><span>6.</span> I'm not sure which plan to pick — what do I do?</h5>
                    <p class="color--grey">Book a free 15-minute discovery call via WhatsApp or email and we'll recommend the best fit for your background and goals.</p>
                  </div>

                </div>
              </div>

            </div>
          </div>

        </div>
      </section>

    </div>
  </NuxtLayout>
</template>

<script setup>
useHead({ title: 'Pricing — CODECV' })

const acceleratorFeatures = [
  { text: 'CV Writing (PT/EN)',           ok: true  },
  { text: 'Cover Letter Writing',         ok: true  },
  { text: 'CV Grammar Review',            ok: true  },
  { text: 'LinkedIn Profile Optimisation',ok: true  },
  { text: 'WhatsApp Support',             ok: true  },
  { text: 'Delivery: ~5 Business Days',   ok: true  },
  { text: 'Video Call Session',           ok: false },
  { text: 'Interview Preparation',        ok: false },
  { text: '1-on-1 Mentorship',            ok: false },
]

const bootcampFeatures = [
  { text: 'Full Laravel Stack Training',  ok: true  },
  { text: 'New Relic Observability',      ok: true  },
  { text: 'Weekly Live Video Classes',    ok: true  },
  { text: 'Code Reviews & Feedback',      ok: true  },
  { text: 'WhatsApp Group Support',       ok: true  },
  { text: 'Certificate of Completion',    ok: true  },
  { text: 'CV & LinkedIn Review',         ok: true  },
  { text: 'Interview Preparation',        ok: true  },
  { text: '1-on-1 Mentorship',            ok: false },
]

const compareRows = [
  { feature: 'CV Writing (PT/EN)',       accelerator: true,  bootcamp: true,  mentorship: true  },
  { feature: 'LinkedIn Profile',         accelerator: true,  bootcamp: true,  mentorship: true  },
  { feature: 'WhatsApp Support',         accelerator: true,  bootcamp: true,  mentorship: true  },
  { feature: 'Interview Preparation',    accelerator: false, bootcamp: true,  mentorship: true  },
  { feature: 'Laravel Stack Training',   accelerator: false, bootcamp: true,  mentorship: false },
  { feature: 'New Relic Observability',  accelerator: false, bootcamp: true,  mentorship: true  },
  { feature: 'Certificate of Completion',accelerator: false, bootcamp: true,  mentorship: false },
  { feature: '1-on-1 Sessions',          accelerator: false, bootcamp: false, mentorship: true  },
  { feature: 'Personalised Roadmap',     accelerator: false, bootcamp: false, mentorship: true  },
  { feature: 'Support Until Hired',      accelerator: false, bootcamp: false, mentorship: true  },
]

const mentorshipFeatures = [
  'Bi-Weekly 1-on-1 Sessions',
  'Personalised Career Roadmap',
  'CV & LinkedIn Review',
  'Interview Coaching',
  'Job Search Strategy',
  'WhatsApp 24/7 Support',
  'Support Until Hired',
  'New Relic Observability Intro',
  'Access to Bootcamp Materials',
]
</script>
