<template>
  <NuxtLayout name="marketing">

    <!-- HERO -->
    <section class="hero">
      <!-- Full-width background image + overlay -->
      <div class="hero__bg">
        <img src="/images/devnew.png" alt="IT career" class="hero__bg-img">
        <div class="hero__bg-overlay" />
      </div>

      <div class="hero__inner">
        <div class="hero__content">
          <div class="hero__badge">
            <span class="hero__badge-dot" />
            Ireland's #1 IT Career Coaching Platform
          </div>

          <h1 class="hero__title">
            The growth accelerator<br>for your <em>IT career</em>
          </h1>

          <p class="hero__desc">
            Expert coaching, curated learning paths, and real job opportunities —
            everything you need to land your next IT role in Ireland.
          </p>

          <div class="hero__ctas">
            <NuxtLink to="/login" class="mkt-btn mkt-btn--primary mkt-btn--lg">
              Get Started for Free
            </NuxtLink>
            <NuxtLink to="/pricing" class="mkt-btn mkt-btn--outline-white mkt-btn--lg">
              View Pricing
            </NuxtLink>
          </div>

          <div class="hero__proof">
            <div class="hero__avatars">
              <span class="hero__avatar" style="background:#f9a8d4;">T</span>
              <span class="hero__avatar" style="background:#86efac;">M</span>
              <span class="hero__avatar" style="background:#93c5fd;">A</span>
              <span class="hero__avatar hero__avatar--plus">+</span>
            </div>
            <div class="hero__proof-text">
              <strong>200+</strong> professionals coached
              <span>for IT roles in Ireland</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Floating cards anchored to hero -->
      <div class="hero__float hero__float--1">
        <div class="hero__float-icon">
          <UIcon name="i-heroicons-document-check" style="width:18px;height:18px;color:#059669;" />
        </div>
        <div>
          <div class="hero__float-label">CV Reviewed</div>
          <div class="hero__float-sub">Ready to submit</div>
        </div>
      </div>

      <div class="hero__float hero__float--2">
        <div class="hero__float-icon hero__float-icon--green">
          <UIcon name="i-heroicons-briefcase" style="width:18px;height:18px;color:#059669;" />
        </div>
        <div>
          <div class="hero__float-label">Job Matched</div>
          <div class="hero__float-sub">3 new opportunities</div>
        </div>
      </div>
    </section>

    <!-- STATS -->
    <section class="stats-section">
      <div class="mkt-container">
        <div class="stats-bar">
          <div v-for="(s, i) in stats" :key="s.label" class="stats-bar__item">
            <div class="stats-bar__icon">
              <UIcon :name="s.icon" style="width:22px;height:22px;" />
            </div>
            <div class="stats-bar__num">{{ s.num }}</div>
            <div class="stats-bar__label">{{ s.label }}</div>
            <div v-if="i < stats.length - 1" class="stats-bar__sep" />
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="features-section">
      <div class="hero__blob hero__blob--3" />
      <div class="mkt-container">
        <div class="section-header">
          <span class="section-badge">What we offer</span>
          <h2 class="section-title">Everything you need to advance your IT career</h2>
          <p class="section-sub">From CV writing to job placement — we've got you covered.</p>
        </div>

        <div class="features-grid">
          <div v-for="feat in features" :key="feat.title" class="feat-card">
            <div class="feat-card__icon" :style="`background: ${feat.bg}`">
              <UIcon :name="feat.icon" style="width:22px;height:22px;" :style="`color: ${feat.color}`" />
            </div>
            <h3 class="feat-card__title">{{ feat.title }}</h3>
            <p class="feat-card__desc">{{ feat.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how-section">
      <div class="mkt-container">
        <div class="how-inner">
          <div class="how-left">
            <div class="how-img-wrap">
              <img src="/images/img-07.png" alt="Career coaching session" class="how-img">
              <div class="how-img-card">
                <div class="how-img-card__icon">
                  <UIcon name="i-heroicons-check-circle" style="width:20px;height:20px;color:#059669;" />
                </div>
                <div>
                  <div class="how-img-card__label">Success Rate</div>
                  <div class="how-img-card__val">92% placement</div>
                </div>
              </div>
            </div>
          </div>
          <div class="how-right">
            <span class="section-badge">How It Works</span>
            <h2 class="section-title" style="text-align:left;margin:0 0 14px;">Your career accelerator in three steps</h2>
            <p class="section-sub" style="text-align:left;margin:0 0 36px;">We pair you with an expert consultant, build your personalised plan, then support you all the way to your next job offer.</p>
            <div class="steps-list">
              <div v-for="(step, i) in steps" :key="step.title" class="step-item">
                <div class="step-item__num">{{ String(i + 1).padStart(2, '0') }}</div>
                <div class="step-item__body">
                  <h3 class="step-item__title">{{ step.title }}</h3>
                  <p class="step-item__desc">{{ step.desc }}</p>
                </div>
              </div>
            </div>
            <NuxtLink to="/login" class="mkt-btn mkt-btn--primary mkt-btn--lg" style="margin-top:32px;">Start Now</NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonials-section">
      <div class="hero__blob hero__blob--4" />
      <div class="mkt-container">
        <div class="section-header">
          <span class="section-badge">Success Stories</span>
          <h2 class="section-title">What our clients say</h2>
          <p class="section-sub">Real stories from IT professionals who transformed their careers.</p>
        </div>

        <div class="testimonials-grid">
          <div v-for="t in testimonials" :key="t.author" class="tcard">
            <div class="tcard__stars">★★★★★</div>
            <p class="tcard__text">"{{ t.text }}"</p>
            <div class="tcard__author">
              <img :src="t.img" :alt="t.author" class="tcard__avatar-img">
              <div>
                <div class="tcard__name">{{ t.author }}</div>
                <div class="tcard__role">{{ t.role }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA BANNER -->
    <section class="cta-section">
      <div class="mkt-container">
        <div class="cta-box">
          <div class="cta-box__blob" />
          <span class="section-badge" style="color:#fff;background:rgba(255,255,255,0.15);">Get Started</span>
          <h2 class="cta-box__title">Start accelerating your IT career today</h2>
          <p class="cta-box__sub">No credit card required to get started.</p>
          <div class="cta-box__btns">
            <NuxtLink to="/login" class="mkt-btn mkt-btn--white mkt-btn--lg">Get started — it's free</NuxtLink>
            <NuxtLink to="/pricing" class="mkt-btn mkt-btn--lg" style="color:#fff;border-color:rgba(255,255,255,0.35);background:transparent;">View pricing</NuxtLink>
          </div>
        </div>
      </div>
    </section>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })
useSeoMeta({
  title: 'IT Career Coaching Platform for Ireland',
  description: 'CODECV — AI-powered CV analysis, LinkedIn optimisation, structured learning paths, and 1-on-1 coaching for IT professionals in Ireland.',
  ogTitle: 'CODECV — IT Career Coaching for Ireland',
  ogDescription: 'AI-powered CV analysis, LinkedIn optimisation, structured learning paths, and 1-on-1 coaching for IT professionals in Ireland.',
  twitterCard: 'summary_large_image',
})

const stats = [
  { icon: 'i-heroicons-users', num: '200+', label: 'Professionals Coached' },
  { icon: 'i-heroicons-briefcase', num: '92%', label: 'Job Placement Rate' },
  { icon: 'i-heroicons-star', num: '4.9★', label: 'Client Satisfaction' },
]

const features = [
  {
    icon: 'i-heroicons-document-text',
    bg: '#f0edff', color: '#059669',
    title: 'Professional CV Writing',
    desc: 'Bespoke CV writing tailored for the Irish IT market. Our experts know what hiring managers look for and craft your story to stand out.',
  },
  {
    icon: 'i-heroicons-map',
    bg: '#ecfdf5', color: '#059669',
    title: 'Structured Learning Paths',
    desc: 'Follow curated roadmaps tailored to your target role — from junior dev to cloud architect, with real courses and milestones.',
  },
  {
    icon: 'i-heroicons-user-group',
    bg: '#fdf4ff', color: '#9333ea',
    title: '1-on-1 Expert Coaching',
    desc: 'Work with seasoned IT consultants who know the Irish job market inside out. Get personalised guidance and interview preparation.',
  },
  {
    icon: 'i-heroicons-briefcase',
    bg: '#fff7ed', color: '#ea580c',
    title: 'Exclusive Job Listings',
    desc: 'Access curated IT job opportunities matched to your skills and experience. Direct connections to top companies in Dublin.',
  },
  {
    icon: 'i-heroicons-magnifying-glass',
    bg: '#eff6ff', color: '#2563eb',
    title: 'LinkedIn & ATS Optimisation',
    desc: 'Get your LinkedIn profile and CV optimised for applicant tracking systems. Be found by the right recruiters.',
  },
  {
    icon: 'i-heroicons-chart-bar',
    bg: '#fefce8', color: '#ca8a04',
    title: 'Progress Tracking Dashboard',
    desc: 'Monitor your learning progress, completed courses, and application activity — all in one personal dashboard.',
  },
]

const steps = [
  {
    title: 'Create your profile',
    desc: 'Tell us where you are in your career and where you want to go. Our team reviews your background.',
  },
  {
    title: 'Get matched with a consultant',
    desc: 'Your dedicated coach builds your personalised learning path and CV strategy.',
  },
  {
    title: 'Land the job',
    desc: 'Follow your path, apply to curated roles, and get hired with ongoing coaching support.',
  },
]

const testimonials = [
  {
    text: 'CODECV helped me land a senior developer role in Dublin within 3 months. The CV rewrite and coaching sessions were game-changing.',
    author: 'Tomasz K.',
    role: 'Senior Java Developer',
    img: '/images/review-author-1.jpg',
  },
  {
    text: 'The structured learning path gave me a clear roadmap. I went from junior to mid-level engineer in 6 months following the programme.',
    author: 'Maria S.',
    role: 'Software Engineer',
    img: '/images/review-author-2.jpg',
  },
  {
    text: 'Incredible support throughout. My consultant knew exactly which companies were hiring and helped me tailor my applications.',
    author: 'Arun P.',
    role: 'Cloud Engineer',
    img: '/images/review-author-3.jpg',
  },
  {
    text: 'I was stuck in the same role for 3 years. CODECV\'s coaching and the job board helped me move into a tech lead position within 4 months.',
    author: 'Ciara M.',
    role: 'Tech Lead',
    img: '/images/review-author-4.jpg',
  },
]
</script>

<style scoped>
/* BLOBS (shared across sections) */
/* HERO - full-width background */
.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding-top: 80px;
  overflow: hidden;
}

/* Background image layer */
.hero__bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}
.hero__bg-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: 72% center;
  display: block;
}
.hero__bg-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to right,
    rgba(6, 2, 18, 0.95) 0%,
    rgba(6, 2, 18, 0.90) 22%,
    rgba(6, 2, 18, 0.65) 40%,
    rgba(6, 2, 18, 0.22) 58%,
    rgba(6, 2, 18, 0.02) 75%,
    transparent 100%
  );
}

/* Content */
.hero__inner {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  min-height: calc(100vh - 80px);
  padding-top: 40px;
  padding-bottom: 100px;
  padding-left: clamp(28px, 7vw, 120px);
  padding-right: 28px;
}
.hero__content {
  max-width: 440px;
  animation: fadeUp 0.7s ease both;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Badge */
.hero__badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.12);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.22);
  backdrop-filter: blur(8px);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.04em;
  padding: 6px 16px;
  border-radius: 50px;
  margin-bottom: 24px;
}
.hero__badge-dot {
  display: inline-block;
  width: 7px; height: 7px;
  background: #a78bfa;
  border-radius: 50%;
  animation: pulse 2s ease infinite;
}
@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(167,139,250,0.5); }
  50% { box-shadow: 0 0 0 5px rgba(167,139,250,0); }
}

/* Title */
.hero__title {
  font-size: clamp(34px, 4.2vw, 58px);
  font-weight: 800;
  color: #fff;
  line-height: 1.1;
  letter-spacing: -0.04em;
  margin-bottom: 20px;
}
.hero__title em {
  font-style: normal;
  color: #a78bfa;
}

/* Desc */
.hero__desc {
  font-size: 17px;
  color: rgba(255,255,255,0.72);
  line-height: 1.7;
  max-width: 480px;
  margin-bottom: 36px;
}

/* CTAs */
.hero__ctas {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 40px;
}
.mkt-btn--outline-white {
  color: #fff;
  border: 2px solid rgba(255,255,255,0.45);
  background: transparent;
}
.mkt-btn--outline-white:hover {
  background: rgba(255,255,255,0.1);
  border-color: rgba(255,255,255,0.75);
}

/* Social proof */
.hero__proof {
  display: flex;
  align-items: center;
  gap: 14px;
}
.hero__avatars { display: flex; }
.hero__avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px; height: 36px;
  border-radius: 50%;
  font-size: 13px;
  font-weight: 700;
  color: #333;
  border: 2px solid rgba(255,255,255,0.6);
  margin-right: -8px;
}
.hero__avatar--plus {
  background: var(--accent);
  color: #fff;
  font-size: 16px;
}
.hero__proof-text {
  font-size: 13px;
  color: rgba(255,255,255,0.7);
  line-height: 1.5;
  display: flex;
  flex-direction: column;
}
.hero__proof-text strong { color: #fff; }

/* Floating cards */
.hero__float {
  position: absolute;
  z-index: 2;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.9);
  border-radius: 14px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  white-space: nowrap;
}
.hero__float--1 {
  bottom: 220px;
  right: 6%;
  animation: floatUp 3s ease-in-out infinite;
}
.hero__float--2 {
  bottom: 130px;
  right: 6%;
  animation: floatUp 3.5s ease-in-out infinite 0.5s;
}
@keyframes floatUp {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-6px); }
}
.hero__float-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px; height: 36px;
  border-radius: 10px;
  background: var(--accent-light);
  flex-shrink: 0;
}
.hero__float-icon--green { background: #ecfdf5; }
.hero__float-label {
  font-size: 13px;
  font-weight: 700;
  color: var(--text);
}
.hero__float-sub {
  font-size: 11px;
  color: var(--muted);
}

/* STATS */
.stats-section {
  padding: 0;
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  background: var(--bg);
}
.stats-bar {
  display: flex;
  align-items: center;
}
.stats-bar__item {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 28px 32px;
  position: relative;
}
.stats-bar__icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px; height: 44px;
  border-radius: 12px;
  background: var(--accent-light);
  color: var(--accent);
  flex-shrink: 0;
}
.stats-bar__num {
  font-size: 32px;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.04em;
  line-height: 1;
}
.stats-bar__label {
  font-size: 13px;
  color: var(--muted);
  font-weight: 500;
  line-height: 1.3;
}
.stats-bar__sep {
  position: absolute;
  right: 0; top: 20%; bottom: 20%;
  width: 1px;
  background: var(--border);
}

/* FEATURES */
.features-section {
  position: relative;
  padding: 100px 0;
  overflow: hidden;
}
.features-grid {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}
.feat-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-card);
  padding: 28px 24px;
  transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
  box-shadow: var(--shadow-sm);
}
.feat-card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-3px);
  border-color: rgba(5, 150, 105, 0.15);
}
.feat-card__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 46px; height: 46px;
  border-radius: 12px;
  margin-bottom: 16px;
}
.feat-card__title {
  font-size: 16px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 8px;
  letter-spacing: -0.02em;
}
.feat-card__desc {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.65;
}

/* HOW IT WORKS */
.how-section {
  padding: 100px 0;
  background: var(--bg-soft);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}
.how-inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 72px;
  align-items: center;
}
.how-left { position: relative; }
.how-img-wrap { position: relative; }
.how-img {
  width: 100%;
  border-radius: 20px;
  display: block;
  box-shadow: var(--shadow-md);
  object-fit: cover;
  max-height: 520px;
}
.how-img-card {
  position: absolute;
  bottom: 24px;
  left: -20px;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.9);
  border-radius: 14px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: var(--shadow-md);
}
.how-img-card__icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px; height: 36px;
  border-radius: 10px;
  background: #ecfdf5;
  flex-shrink: 0;
}
.how-img-card__label { font-size: 11px; color: var(--muted); font-weight: 500; }
.how-img-card__val { font-size: 14px; font-weight: 700; color: var(--text); }
.how-right {
  display: flex;
  flex-direction: column;
}
.steps-list { display: flex; flex-direction: column; }
.step-item {
  display: flex;
  gap: 20px;
  padding: 24px 0;
  border-bottom: 1px solid var(--border);
}
.step-item:last-child { border-bottom: none; }
.step-item__num {
  font-size: 13px;
  font-weight: 800;
  color: var(--accent);
  opacity: 0.5;
  flex-shrink: 0;
  padding-top: 3px;
  letter-spacing: 0.04em;
  min-width: 28px;
}
.step-item__title {
  font-size: 17px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 6px;
  letter-spacing: -0.02em;
}
.step-item__desc {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.65;
}

/* TESTIMONIALS */
.testimonials-section {
  position: relative;
  padding: 100px 0;
  overflow: hidden;
}
.testimonials-grid {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}
.tcard {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-card);
  padding: 28px;
  box-shadow: var(--shadow-sm);
  transition: box-shadow 0.2s, transform 0.2s;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.tcard:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}
.tcard__stars {
  font-size: 14px;
  color: #f59e0b;
  letter-spacing: 2px;
}
.tcard__text {
  font-size: 15px;
  color: var(--text-body);
  line-height: 1.7;
  flex: 1;
}
.tcard__author {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-top: 4px;
  border-top: 1px solid var(--border);
}
.tcard__avatar-img {
  width: 40px; height: 40px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
  border: 2px solid var(--border);
}
.tcard__name {
  font-size: 14px;
  font-weight: 700;
  color: var(--text);
}
.tcard__role {
  font-size: 12px;
  color: var(--muted);
}

/* CTA */
.cta-section { padding: 80px 0 100px; }
.cta-box {
  position: relative;
  background: linear-gradient(135deg, #059669 0%, #10b981 60%, #10b981 100%);
  border-radius: 24px;
  padding: 80px 56px;
  text-align: center;
  overflow: hidden;
}
.cta-box__blob {
  position: absolute;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 65%);
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  pointer-events: none;
}
.cta-box__title {
  font-size: clamp(28px, 3.5vw, 46px);
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.04em;
  line-height: 1.15;
  margin-bottom: 14px;
  position: relative;
}
.cta-box__sub {
  font-size: 15px;
  color: rgba(255,255,255,0.7);
  margin-bottom: 36px;
  position: relative;
}
.cta-box__btns {
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
  position: relative;
}

/* RESPONSIVE */
@media (max-width: 960px) {
  .hero__bg-overlay {
    background: rgba(6, 2, 18, 0.75);
  }
  .hero__inner { padding-left: 28px; }
  .hero__content { max-width: 100%; }
  .hero__float { display: none; }
  .how-inner { grid-template-columns: 1fr; gap: 40px; }
  .how-img-card { left: 0; }
}
@media (max-width: 768px) {
  .features-grid { grid-template-columns: repeat(2, 1fr); }
  .stats-bar { flex-direction: column; }
  .stats-bar__sep { display: none; }
  .stats-bar__item { width: 100%; border-bottom: 1px solid var(--border); }
  .stats-bar__item:last-child { border-bottom: none; }
}
@media (max-width: 580px) {
  .features-grid { grid-template-columns: 1fr; }
  .testimonials-grid { grid-template-columns: 1fr; }
  .cta-box { padding: 48px 24px; }
}
</style>
