<template>
  <NuxtLayout name="marketing">

    <!-- HERO -->
    <section class="ph">
      <div class="ph__blob" />
      <div class="mkt-container ph__content">
        <span class="section-badge">Pricing</span>
        <h1 class="ph__title">Simple, flexible pricing</h1>
        <p class="ph__sub">Choose the plan that fits your career goals — from self-paced CV services to full bootcamp training.</p>
      </div>
    </section>

    <!-- CARDS -->
    <section id="plans" class="plans-section">
      <div class="mkt-container">
        <div class="plans-grid">

          <!-- Career Accelerator -->
          <div class="pcard">
            <div class="pcard__tier">Starter</div>
            <h2 class="pcard__name">Career Accelerator</h2>
            <p class="pcard__desc">One-time, self-serve delivery</p>
            <div class="pcard__price-row">
              <span class="pcard__currency">€</span>
              <span class="pcard__amount">99</span>
            </div>
            <p class="pcard__billing">one-time payment</p>
            <ul class="pcard__features">
              <li v-for="f in acceleratorFeatures" :key="f.text" :class="['pcard__feat', { 'pcard__feat--off': !f.ok }]">
                <span :class="['pcard__check', { 'pcard__check--off': !f.ok }]">{{ f.ok ? '✓' : '✗' }}</span>
                {{ f.text }}
              </li>
            </ul>
            <a href="https://buy.stripe.com/28EfZg8umaHK7VC7CT7Zu01" target="_blank" class="mkt-btn mkt-btn--outline mkt-btn--lg pcard__cta">Get Started</a>
          </div>

          <!-- Bootcamp — Featured -->
          <div class="pcard pcard--featured">
            <div class="pcard__best-badge">Best Value</div>
            <div class="pcard__tier" style="color:rgba(255,255,255,0.65)">Bootcamp</div>
            <h2 class="pcard__name" style="color:#fff">Laravel + NR Bootcamp</h2>
            <p class="pcard__desc" style="color:rgba(255,255,255,0.55)">Cohort-based, 12–16 weeks</p>
            <div class="pcard__price-row">
              <span class="pcard__currency" style="color:#fff">€</span>
              <span class="pcard__amount" style="color:#fff">1,500</span>
            </div>
            <p class="pcard__billing" style="color:rgba(255,255,255,0.5)">per student</p>
            <ul class="pcard__features">
              <li v-for="f in bootcampFeatures" :key="f.text" :class="['pcard__feat pcard__feat--light', { 'pcard__feat--off-light': !f.ok }]">
                <span class="pcard__check pcard__check--light">{{ f.ok ? '✓' : '✗' }}</span>
                {{ f.text }}
              </li>
            </ul>
            <a href="https://buy.stripe.com/3cI4gy3a29DG5Nu6yP7Zu02" target="_blank" class="mkt-btn mkt-btn--white mkt-btn--lg pcard__cta">Enroll Now</a>
          </div>

          <!-- Mentorship -->
          <div class="pcard">
            <div class="pcard__tier">Premium</div>
            <h2 class="pcard__name">1-on-1 Mentorship</h2>
            <p class="pcard__desc">Ongoing, personalised support</p>
            <div class="pcard__price-row">
              <span class="pcard__currency">€</span>
              <span class="pcard__amount">249</span>
            </div>
            <p class="pcard__billing">per month · cancel anytime</p>
            <ul class="pcard__features">
              <li v-for="f in mentorshipFeatures" :key="f" class="pcard__feat">
                <span class="pcard__check">✓</span>
                {{ f }}
              </li>
            </ul>
            <a href="https://buy.stripe.com/00w5kC25YeY04JqaP57Zu03" target="_blank" class="mkt-btn mkt-btn--outline mkt-btn--lg pcard__cta">Get Started</a>
          </div>

        </div>
      </div>
    </section>

    <!-- COMPARE -->
    <section class="compare-section">
      <div class="mkt-container">
        <div class="section-header">
          <span class="section-badge">Full breakdown</span>
          <h2 class="section-title">Compare plans</h2>
          <p class="section-sub">Detailed feature breakdown across all pricing tiers.</p>
        </div>
        <div class="compare-wrap">
          <table class="ctable">
            <thead>
              <tr>
                <th class="ctable__lh">Feature</th>
                <th>
                  <div class="ctable__pname">Career Accelerator</div>
                  <div class="ctable__pprice">€99 one-time</div>
                </th>
                <th class="ctable__fcol">
                  <div class="ctable__pname" style="color:var(--accent)">Bootcamp</div>
                  <div class="ctable__pprice" style="color:rgba(5,150,105,.6)">€1,500 one-time</div>
                </th>
                <th>
                  <div class="ctable__pname">Mentorship</div>
                  <div class="ctable__pprice">€249/month</div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, i) in compareRows" :key="row.feature" :class="{ 'ctable__row--alt': i % 2 !== 0 }">
                <td class="ctable__feat">{{ row.feature }}</td>
                <td class="ctable__cell">
                  <span :class="row.accelerator ? 'ctable__yes' : 'ctable__no'">{{ row.accelerator ? '✓' : '–' }}</span>
                </td>
                <td class="ctable__cell ctable__fcol">
                  <span :class="row.bootcamp ? 'ctable__yes ctable__yes--p' : 'ctable__no'">{{ row.bootcamp ? '✓' : '–' }}</span>
                </td>
                <td class="ctable__cell">
                  <span :class="row.mentorship ? 'ctable__yes' : 'ctable__no'">{{ row.mentorship ? '✓' : '–' }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- SOCIAL PROOF -->
    <section class="proof-section">
      <div class="mkt-container">
        <div class="proof-inner">
          <div class="proof-img-col">
            <img src="/images/img-07.png" alt="Career success" class="proof-img">
            <div class="proof-img-badge">
              <div class="proof-img-badge__avatars">
                <img src="/images/review-author-1.jpg" class="proof-img-badge__av" alt="">
                <img src="/images/review-author-2.jpg" class="proof-img-badge__av" alt="">
                <img src="/images/review-author-3.jpg" class="proof-img-badge__av" alt="">
              </div>
              <div>
                <div class="proof-img-badge__num">200+</div>
                <div class="proof-img-badge__label">professionals placed</div>
              </div>
            </div>
          </div>
          <div class="proof-text-col">
            <span class="section-badge">Why it works</span>
            <h2 class="proof-title">Real results for IT professionals in Ireland</h2>
            <p class="proof-sub">Our clients don't just get a CV — they get a complete career strategy backed by consultants who know the Irish market inside out.</p>
            <div class="proof-stats">
              <div class="proof-stat">
                <span class="proof-stat__num">92%</span>
                <span class="proof-stat__label">Job placement rate</span>
              </div>
              <div class="proof-stat">
                <span class="proof-stat__num">+20%</span>
                <span class="proof-stat__label">Average salary increase</span>
              </div>
              <div class="proof-stat">
                <span class="proof-stat__num">4.9★</span>
                <span class="proof-stat__label">Client satisfaction</span>
              </div>
            </div>
            <NuxtLink to="/pricing#plans" class="mkt-btn mkt-btn--primary mkt-btn--lg" style="margin-top:8px;">Choose a plan</NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="pfaq-section">
      <div class="mkt-container">
        <div class="section-header">
          <span class="section-badge">Questions</span>
          <h2 class="section-title">Common questions</h2>
        </div>
        <div class="pfaq-list">
          <div v-for="(faq, i) in faqs" :key="i" class="pfaq-item" @click="open = open === i ? -1 : i">
            <div class="pfaq-item__q">
              <span>{{ faq.q }}</span>
              <svg :class="['pfaq-item__ico', { 'pfaq-item__ico--open': open === i }]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <Transition name="ans">
              <p v-if="open === i" class="pfaq-item__a">{{ faq.a }}</p>
            </Transition>
          </div>
        </div>
      </div>
    </section>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })
useSeoMeta({
  title: 'Pricing',
  description: 'Three plans for IT career growth in Ireland: from AI-powered self-service tools to 1-on-1 coaching with senior consultants. Choose what fits your goals.',
  ogTitle: 'CODECV Pricing — Plans for Every Career Stage',
  ogDescription: 'From AI-powered self-service tools to 1-on-1 coaching with senior consultants. Choose the plan that matches your IT career goals in Ireland.',
  twitterCard: 'summary_large_image',
})

// Product structured data — Google may surface these in rich SERP results
// (price, availability, plan name) for queries about CV writing services.
// Explicit @id required because @nuxtjs/seo dedupes Products without one.
useSchemaOrg([
  defineProduct({
    '@id': 'https://codecv.ie/pricing#career-accelerator',
    name: 'Career Accelerator',
    description: 'CV writing, LinkedIn optimisation, and cover letter for IT professionals. Delivered within 5 business days.',
    offers: [{ '@type': 'Offer', price: 99, priceCurrency: 'EUR', availability: 'https://schema.org/InStock', url: 'https://codecv.ie/pricing' }],
    brand: { '@type': 'Brand', name: 'CODECV' },
  }),
  defineProduct({
    '@id': 'https://codecv.ie/pricing#bootcamp',
    name: 'Laravel + NR Bootcamp',
    description: 'Cohort-based 12–16 week training programme covering Laravel architecture and New Relic observability — production-grade engineering for Irish tech roles.',
    offers: [{ '@type': 'Offer', price: 1500, priceCurrency: 'EUR', availability: 'https://schema.org/InStock', url: 'https://codecv.ie/pricing' }],
    brand: { '@type': 'Brand', name: 'CODECV' },
  }),
  defineProduct({
    '@id': 'https://codecv.ie/pricing#mentorship',
    name: '1-on-1 Mentorship',
    description: 'Two 60-minute monthly video coaching sessions plus WhatsApp support. Cancel anytime.',
    offers: [{ '@type': 'Offer', price: 249, priceCurrency: 'EUR', availability: 'https://schema.org/InStock', url: 'https://codecv.ie/pricing' }],
    brand: { '@type': 'Brand', name: 'CODECV' },
  }),
])

const open = ref(0)

const acceleratorFeatures = [
  { text: 'CV Writing (PT/EN)', ok: true },
  { text: 'Cover Letter Writing', ok: true },
  { text: 'CV Grammar Review', ok: true },
  { text: 'LinkedIn Profile Optimisation', ok: true },
  { text: 'WhatsApp Support', ok: true },
  { text: 'Delivery: ~5 Business Days', ok: true },
  { text: 'Video Call Session', ok: false },
  { text: 'Interview Preparation', ok: false },
  { text: '1-on-1 Mentorship', ok: false },
]
const bootcampFeatures = [
  { text: 'Full Laravel Stack Training', ok: true },
  { text: 'New Relic Observability', ok: true },
  { text: 'Weekly Live Video Classes', ok: true },
  { text: 'Code Reviews & Feedback', ok: true },
  { text: 'WhatsApp Group Support', ok: true },
  { text: 'Certificate of Completion', ok: true },
  { text: 'CV & LinkedIn Review', ok: true },
  { text: 'Interview Preparation', ok: true },
  { text: '1-on-1 Mentorship', ok: false },
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
const compareRows = [
  { feature: 'CV Writing (PT/EN)', accelerator: true, bootcamp: true, mentorship: true },
  { feature: 'LinkedIn Profile', accelerator: true, bootcamp: true, mentorship: true },
  { feature: 'WhatsApp Support', accelerator: true, bootcamp: true, mentorship: true },
  { feature: 'Interview Preparation', accelerator: false, bootcamp: true, mentorship: true },
  { feature: 'Laravel Stack Training', accelerator: false, bootcamp: true, mentorship: false },
  { feature: 'New Relic Observability', accelerator: false, bootcamp: true, mentorship: true },
  { feature: 'Certificate of Completion', accelerator: false, bootcamp: true, mentorship: false },
  { feature: '1-on-1 Sessions', accelerator: false, bootcamp: false, mentorship: true },
  { feature: 'Personalised Roadmap', accelerator: false, bootcamp: false, mentorship: true },
  { feature: 'Support Until Hired', accelerator: false, bootcamp: false, mentorship: true },
]
const faqs = [
  { q: 'What is included in the Career Accelerator?', a: 'The Career Accelerator delivers a complete CV rewrite in English and/or Portuguese, LinkedIn profile optimization, and a tailored cover letter — everything you need to start landing more interviews.' },
  { q: 'Who is the Bootcamp for?', a: "The Laravel + New Relic Bootcamp is designed for junior to mid-level developers who want to break into production-level engineering. You'll learn real Laravel architecture and New Relic observability — skills that get you hired." },
  { q: 'How do the bi-weekly sessions work?', a: 'Mentorship sessions are 60-minute video calls twice a month. Between sessions you get WhatsApp support and access to all recorded Bootcamp materials.' },
  { q: 'How do I pay?', a: "Click any plan button to go directly to our secure Stripe checkout. Payment is processed instantly and you'll receive a confirmation email right away." },
  { q: 'Can I get a refund?', a: 'Career Accelerator and Bootcamp enrollment are non-refundable once work has started. Mentorship plans can be cancelled at any time before the next billing cycle.' },
  { q: "I'm not sure which plan to pick — what do I do?", a: "Book a free 15-minute discovery call via WhatsApp or email and we'll recommend the best fit for your background and goals." },
]
</script>

<style scoped>
/* HERO */
.ph {
  position: relative;
  padding: 160px 0 80px;
  text-align: center;
  overflow: hidden;
}
.ph__blob {
  position: absolute;
  width: 700px; height: 400px;
  background: radial-gradient(ellipse, rgba(5,150,105,0.1) 0%, transparent 65%);
  top: -50px; left: 50%; transform: translateX(-50%);
  filter: blur(50px);
  pointer-events: none;
}
.ph__content { position: relative; z-index: 1; animation: fadeUp .7s ease both; }
@keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.ph__title {
  font-size: clamp(34px, 5vw, 60px);
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.04em;
  line-height: 1.1;
  margin: 14px 0 18px;
}
.ph__sub { font-size: 17px; color: var(--muted); max-width: 500px; margin: 0 auto; line-height: 1.7; }

/* PLANS */
.plans-section { padding: 20px 0 100px; }
.plans-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  align-items: start;
}
.pcard {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 32px 28px;
  display: flex;
  flex-direction: column;
  box-shadow: var(--shadow-sm);
  transition: box-shadow .2s, transform .2s;
  position: relative;
}
.pcard:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
.pcard--featured {
  background: linear-gradient(145deg, #059669 0%, #10b981 100%);
  border-color: #059669;
  box-shadow: 0 8px 32px rgba(5,150,105,0.35);
  transform: scale(1.02);
}
.pcard--featured:hover { transform: scale(1.02) translateY(-3px); }
.pcard__best-badge {
  position: absolute;
  top: 0; right: 20px;
  background: #f59e0b;
  color: #fff;
  font-size: 10px; font-weight: 800;
  letter-spacing: .08em; text-transform: uppercase;
  padding: 5px 12px;
  border-radius: 0 0 8px 8px;
}
.pcard__tier {
  font-size: 11px; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 8px;
}
.pcard__name {
  font-size: 20px; font-weight: 800;
  color: var(--text); letter-spacing: -.02em;
  margin-bottom: 4px;
}
.pcard__desc { font-size: 13px; color: var(--muted); margin-bottom: 20px; }
.pcard__price-row { display: flex; align-items: flex-start; gap: 2px; }
.pcard__currency { font-size: 18px; font-weight: 700; color: var(--text); margin-top: 10px; }
.pcard__amount {
  font-size: 52px; font-weight: 800;
  color: var(--text); letter-spacing: -.04em; line-height: 1;
}
.pcard__billing { font-size: 13px; color: var(--muted); margin: 4px 0 24px; }
.pcard__features { list-style: none; flex: 1; margin-bottom: 28px; }
.pcard__feat {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 0;
  border-bottom: 1px solid var(--border);
  font-size: 13px; color: var(--text-body);
}
.pcard__feat:last-child { border-bottom: none; }
.pcard__feat--off { color: var(--muted); }
.pcard__feat--light { color: rgba(255,255,255,.85); border-bottom-color: rgba(255,255,255,.1); }
.pcard__feat--off-light { color: rgba(255,255,255,.35); }
.pcard__check {
  display: inline-flex; align-items: center; justify-content: center;
  width: 18px; height: 18px;
  border-radius: 50%;
  background: #ecfdf5; color: #059669;
  font-size: 10px; font-weight: 700;
  flex-shrink: 0;
}
.pcard__check--off { background: #f3f4f6; color: var(--muted); }
.pcard__check--light { background: rgba(255,255,255,.2); color: #fff; }
.pcard__cta { width: 100%; justify-content: center; margin-top: auto; }

/* COMPARE */
.compare-section {
  padding: 80px 0 100px;
  background: var(--bg-soft);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}
.compare-wrap { overflow-x: auto; border-radius: 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
.ctable { width: 100%; border-collapse: collapse; }
.ctable thead tr { background: #fff; }
.ctable th { padding: 18px 20px; text-align: center; border-bottom: 1px solid var(--border); }
.ctable th:first-child { text-align: left; width: 34%; }
.ctable__lh { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }
.ctable__pname { font-size: 13px; font-weight: 700; color: var(--text); }
.ctable__pprice { font-size: 11px; color: var(--muted); margin-top: 2px; }
.ctable__fcol { background: rgba(5,150,105,.03); }
.ctable td { padding: 13px 20px; border-bottom: 1px solid var(--border); }
.ctable tbody tr:last-child td { border-bottom: none; }
.ctable__row--alt td { background: rgba(5,150,105,.015); }
.ctable__feat { font-size: 14px; color: var(--text); font-weight: 500; }
.ctable__cell { text-align: center; }
.ctable__yes {
  display: inline-flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 50%;
  background: #ecfdf5; color: #059669;
  font-size: 11px; font-weight: 700;
}
.ctable__yes--p { background: var(--accent-light); color: var(--accent); }
.ctable__no {
  display: inline-flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 50%;
  background: #f9fafb; color: var(--muted);
  font-size: 13px;
}

/* SOCIAL PROOF */
.proof-section {
  padding: 80px 0;
  background: var(--bg-soft);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}
.proof-inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 72px;
  align-items: center;
}
.proof-img-col { position: relative; }
.proof-img {
  width: 100%;
  border-radius: 20px;
  display: block;
  box-shadow: var(--shadow-md);
  object-fit: cover;
  max-height: 460px;
}
.proof-img-badge {
  position: absolute;
  bottom: 24px; right: -20px;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.9);
  border-radius: 16px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: var(--shadow-md);
}
.proof-img-badge__avatars {
  display: flex;
}
.proof-img-badge__av {
  width: 32px; height: 32px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
  margin-right: -8px;
}
.proof-img-badge__num { font-size: 18px; font-weight: 800; color: var(--text); letter-spacing: -0.02em; }
.proof-img-badge__label { font-size: 12px; color: var(--muted); }
.proof-title {
  font-size: clamp(26px, 3.5vw, 40px);
  font-weight: 800; color: var(--text);
  letter-spacing: -0.04em; line-height: 1.2;
  margin: 14px 0 18px;
}
.proof-sub { font-size: 16px; color: var(--muted); line-height: 1.7; margin-bottom: 32px; }
.proof-stats { display: flex; gap: 32px; flex-wrap: wrap; margin-bottom: 32px; }
.proof-stat { display: flex; flex-direction: column; gap: 3px; }
.proof-stat__num { font-size: 28px; font-weight: 800; color: var(--text); letter-spacing: -0.03em; }
.proof-stat__label { font-size: 12px; color: var(--muted); }

/* FAQ */
.pfaq-section { padding: 80px 0 100px; }
.pfaq-list { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 8px; }
.pfaq-item {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: box-shadow .2s, border-color .2s;
}
.pfaq-item:hover { box-shadow: var(--shadow-md); border-color: rgba(5,150,105,.2); }
.pfaq-item__q {
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
  padding: 20px 24px;
  font-size: 15px; font-weight: 600; color: var(--text);
}
.pfaq-item__ico { flex-shrink: 0; color: var(--muted); transition: transform .3s, color .2s; }
.pfaq-item__ico--open { transform: rotate(180deg); color: var(--accent); }
.pfaq-item__a { padding: 0 24px 20px; font-size: 14px; color: var(--muted); line-height: 1.7; }
.ans-enter-active, .ans-leave-active { transition: all .25s ease; }
.ans-enter-from, .ans-leave-to { opacity: 0; transform: translateY(-6px); }

@media (max-width: 900px) {
  .plans-grid { grid-template-columns: 1fr; max-width: 480px; margin: 0 auto; }
  .pcard--featured { transform: none; }
  .pcard--featured:hover { transform: translateY(-3px); }
  .proof-inner { grid-template-columns: 1fr; gap: 40px; }
  .proof-img-badge { right: 0; }
}
</style>
