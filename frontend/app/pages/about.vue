<template>
  <NuxtLayout name="marketing">

    <!-- HERO -->
    <section class="ah">
      <div class="ah__blob ah__blob--1" />
      <div class="ah__blob ah__blob--2" />
      <div class="mkt-container ah__content">
        <span class="section-badge">About CODECV</span>
        <h1 class="ah__title">We provide solutions<br>for your career path</h1>
        <p class="ah__sub">
          Our professional CV services specialise in the IT career field, writing bespoke CVs for different IT areas.
          When engaging CODECV, you're not purchasing a simple CV re-write; you're partnering with elite application
          experts who know the Irish job market inside out.
        </p>
        <div class="ah__stats">
          <div class="ah__stat">
            <span class="ah__stat-num">200+</span>
            <span class="ah__stat-label">IT professionals coached &amp; placed</span>
          </div>
          <div class="ah__stat-sep" />
          <div class="ah__stat">
            <span class="ah__stat-num">92%</span>
            <span class="ah__stat-label">Job placement success rate</span>
          </div>
          <div class="ah__stat-sep" />
          <div class="ah__stat">
            <span class="ah__stat-num">4.9★</span>
            <span class="ah__stat-label">Average client satisfaction</span>
          </div>
        </div>
      </div>
    </section>

    <!-- PHOTO GALLERY -->
    <section class="gallery-section">
      <div class="mkt-container">
        <div class="gallery-grid">
          <div class="gallery-col gallery-col--left">
            <div class="gallery-img-wrap">
              <img src="/images/a2-1.jpg" alt="CODECV team" class="gallery-img">
            </div>
            <div class="gallery-img-wrap gallery-img-wrap--sm">
              <img src="/images/a2-2.jpg" alt="IT career coaching" class="gallery-img">
            </div>
          </div>
          <div class="gallery-col gallery-col--right">
            <div class="gallery-img-wrap gallery-img-wrap--tall">
              <img src="/images/a2-3.jpg" alt="Professional development" class="gallery-img">
            </div>
            <div class="gallery-img-wrap gallery-img-wrap--sm">
              <img src="/images/a2-4.jpg" alt="Job placement success" class="gallery-img">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOUNDER -->
    <section class="founder">
      <div class="mkt-container">
        <div class="founder__grid">
          <div class="founder__img-col">
            <div class="founder__img-wrap">
              <img src="/images/Leo2.png" alt="Leo Medeiros — CEO & Founder, CODECV" class="founder__img">
              <div class="founder__img-badge">
                <span class="founder__img-badge-icon">★</span>
                CEO &amp; Founder
              </div>
            </div>
          </div>
          <div class="founder__text-col">
            <span class="section-badge">Founder at CODECV</span>
            <h2 class="founder__name">Leo Medeiros</h2>
            <blockquote class="founder__quote">
              "I envision CODECV as a beacon of empowerment, where aspiring developers can flourish, transforming their dreams into tangible realities. Through our personalised training programs, we foster the path to success in the dynamic world of technology."
            </blockquote>
            <p class="founder__bio">
              Inspired by a passion for empowering aspiring developers, Leo Medeiros founded CODECV with the vision of
              bridging the gap between academic knowledge and industry expectations. Recognising the need for tailored
              training to prepare junior developers for professional interviews and the challenges of real-world coding,
              Leo poured his experience into crafting CODECV's comprehensive training programs.
            </p>
            <p class="founder__bio">
              Leo transformed CODECV into a haven for aspiring developers seeking to elevate their skills. The company's
              personalised approach, tailored to each individual's unique profile, equips developers with the confidence,
              expertise, and problem-solving skills to navigate the competitive job market with ease.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- MISSION -->
    <section class="mission-section">
      <div class="mkt-container">
        <div class="section-header">
          <span class="section-badge">What drives us</span>
          <h2 class="section-title">Mission &amp; impact</h2>
        </div>
        <div class="mission-grid">
          <div class="mcard">
            <div class="mcard__icon">
              <UIcon name="i-heroicons-rocket-launch" style="width:22px;height:22px;" />
            </div>
            <h3 class="mcard__title">Mission and impact</h3>
            <p class="mcard__text">To empower individuals to achieve their career goals through comprehensive CV consultancy services. We are committed to helping individuals craft compelling and effective CVs that showcase their unique talents and experiences.</p>
            <ul class="mcard__list">
              <li>
                <span class="mcard__dot" />
                <span><strong>Resume Writing:</strong> We craft a CV that highlights your strongest skills and achievements, ensuring it effectively captures the attention of potential employers.</span>
              </li>
              <li>
                <span class="mcard__dot" />
                <span><strong>Skill Assessment:</strong> We evaluate your skills and experience to identify the most relevant and marketable aspects of your background.</span>
              </li>
            </ul>
          </div>
          <div class="mcard">
            <div class="mcard__icon mcard__icon--green">
              <UIcon name="i-heroicons-chart-bar-square" style="width:22px;height:22px;" />
            </div>
            <h3 class="mcard__title">We deliver results</h3>
            <p class="mcard__text">At CODECV, we are not content with simply crafting CVs; we are committed to delivering tangible results that propel your career forward. Our expertise lies in transforming your skills and experiences into a compelling narrative that resonates with potential employers.</p>
            <p class="mcard__text">On average, CODECV clients secure more interviews and receive a 20% starting salary increase. No client has failed to secure interviews so far — the company experiences a high number of satisfied and repeated buyers.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- TEAM -->
    <section class="team-section">
      <div class="mkt-container">
        <div class="section-header">
          <span class="section-badge">Our Team</span>
          <h2 class="section-title">The people behind CODECV</h2>
          <p class="section-sub">Experienced IT professionals dedicated to accelerating your career in Ireland.</p>
        </div>
        <div class="team-grid">
          <div class="team-card">
            <div class="team-card__img-wrap">
              <img src="/images/Leo2.png" alt="Leo Medeiros" class="team-card__img">
            </div>
            <div class="team-card__info">
              <h3 class="team-card__name">Leo Medeiros</h3>
              <p class="team-card__role">CEO &amp; Founder</p>
              <p class="team-card__bio">IT consultant and career coach with deep expertise in the Irish tech market. Founded CODECV to help professionals break into top companies.</p>
            </div>
          </div>
          <div class="team-card">
            <div class="team-card__img-wrap">
              <img src="/images/Lucas.png" alt="Lucas" class="team-card__img">
            </div>
            <div class="team-card__info">
              <h3 class="team-card__name">Lucas</h3>
              <p class="team-card__role">Career Coach</p>
              <p class="team-card__bio">Specialist in CV writing and LinkedIn optimisation, helping IT professionals stand out in a competitive market.</p>
            </div>
          </div>
          <div class="team-card">
            <div class="team-card__img-wrap">
              <img src="/images/Luciana.png" alt="Luciana" class="team-card__img">
            </div>
            <div class="team-card__info">
              <h3 class="team-card__name">Luciana</h3>
              <p class="team-card__role">Interview Coach</p>
              <p class="team-card__bio">Former recruiter turned career coach. Specialises in interview preparation and salary negotiation for senior IT roles.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="why-section">
      <div class="mkt-container">
        <div class="why-inner">
          <div class="why-left">
            <span class="section-badge">Why Choose Us</span>
            <h2 class="section-title" style="text-align:left;margin:0 0 16px;">With knowledge, skills &amp; hard work</h2>
            <p class="section-sub" style="text-align:left;margin:0 0 16px;">Embark on a journey to career fulfillment with CODECV, where we empower you to achieve your IT aspirations through comprehensive CV consultancy services.</p>
            <p class="why-left__extra">CODECV is here to be your trusted guide, providing you with the knowledge and support you need to embark on your journey towards IT professional success.</p>
          </div>
          <div class="why-right">
            <div v-for="item in whyItems" :key="item.title" class="why-card">
              <div class="why-card__icon">
                <UIcon :name="item.icon" style="width:20px;height:20px;" />
              </div>
              <div>
                <h3 class="why-card__title">{{ item.title }}</h3>
                <p class="why-card__desc">{{ item.desc }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="about-cta">
      <div class="mkt-container">
        <div class="about-cta__box">
          <div class="about-cta__blob" />
          <span class="section-badge" style="color:#fff;background:rgba(255,255,255,.15)">Get Started</span>
          <h2 class="about-cta__title">Ready to accelerate<br>your IT career?</h2>
          <p class="about-cta__sub">With knowledge, skills &amp; hard work — your road to success starts here.</p>
          <div class="about-cta__btns">
            <NuxtLink to="/login" class="mkt-btn mkt-btn--white mkt-btn--lg">Get started for free</NuxtLink>
            <NuxtLink to="/pricing" class="mkt-btn mkt-btn--lg" style="color:#fff;border-color:rgba(255,255,255,.35);background:transparent;">View pricing</NuxtLink>
          </div>
        </div>
      </div>
    </section>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })
useSeoMeta({
  title: 'About Us',
  description: 'Building career infrastructure for IT professionals in Ireland — AI tools, expert consultants, and structured learning paths combined into one platform.',
  ogTitle: 'About CODECV — Engineering the Future of Careers',
  ogDescription: 'Building career infrastructure for IT professionals in Ireland — AI tools, expert consultants, and structured learning paths.',
  twitterCard: 'summary_large_image',
})

const whyItems = [
  { icon: 'i-heroicons-light-bulb', title: 'User Experience', desc: 'We offer simple and practical 1-on-1 interview coaching, enhancing your performance at job interviews.' },
  { icon: 'i-heroicons-arrow-trending-up', title: 'Growth Career', desc: 'We research the best positions for you based on your profile and career goals.' },
  { icon: 'i-heroicons-trophy', title: 'Non-Plagiarised CVs', desc: 'Being genuine CV resume builders, we craft 100% original resumes with zero grammatical or language errors.' },
  { icon: 'i-heroicons-magnifying-glass-circle', title: 'Job Search Strategies', desc: 'Learn how to raise your visibility to access the hidden job market with SMART strategies.' },
]
</script>

<style scoped>
/* HERO */
.ah {
  position: relative;
  padding: 160px 0 80px;
  overflow: hidden;
}
.ah__blob { position: absolute; border-radius: 50%; pointer-events: none; filter: blur(60px); }
.ah__blob--1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(5,150,105,.12) 0%, transparent 65%); top: -150px; right: -100px; }
.ah__blob--2 { width: 400px; height: 400px; background: radial-gradient(circle, rgba(5,150,105,.07) 0%, transparent 65%); bottom: -50px; left: -100px; }
.ah__content { position: relative; z-index: 1; animation: fadeUp .7s ease both; }
@keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.ah__title {
  font-size: clamp(34px, 5vw, 60px);
  font-weight: 800; color: var(--text);
  letter-spacing: -.04em; line-height: 1.1;
  margin: 18px 0 22px;
}
.ah__sub { font-size: 17px; color: var(--muted); max-width: 680px; line-height: 1.75; margin-bottom: 48px; }
.ah__stats { display: flex; align-items: center; gap: 40px; flex-wrap: wrap; }
.ah__stat { display: flex; flex-direction: column; gap: 3px; }
.ah__stat-num { font-size: 32px; font-weight: 800; color: var(--text); letter-spacing: -.03em; }
.ah__stat-label { font-size: 13px; color: var(--muted); }
.ah__stat-sep { width: 1px; height: 40px; background: var(--border); }

/* GALLERY */
.gallery-section {
  padding: 0 0 80px;
  border-top: 1px solid var(--border);
  background: var(--bg-soft);
}
.gallery-grid {
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 12px;
  padding-top: 60px;
}
.gallery-col {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.gallery-img-wrap {
  border-radius: 16px;
  overflow: hidden;
  flex: 1;
}
.gallery-img-wrap--tall { flex: 2; }
.gallery-img-wrap--sm { flex: 0 0 200px; }
.gallery-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}
.gallery-img-wrap:hover .gallery-img { transform: scale(1.03); }

/* TEAM */
.team-section {
  padding: 100px 0;
  border-top: 1px solid var(--border);
}
.team-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.team-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: box-shadow 0.2s, transform 0.2s;
}
.team-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
.team-card__img-wrap {
  height: 280px;
  overflow: hidden;
  background: var(--bg-soft);
}
.team-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top center;
  display: block;
  transition: transform 0.4s ease;
}
.team-card:hover .team-card__img { transform: scale(1.04); }
.team-card__info { padding: 24px; }
.team-card__name {
  font-size: 18px;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.02em;
  margin-bottom: 4px;
}
.team-card__role {
  font-size: 13px;
  font-weight: 600;
  color: var(--accent);
  margin-bottom: 10px;
}
.team-card__bio {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.65;
}

/* FOUNDER */
.founder {
  padding: 80px 0 100px;
  border-top: 1px solid var(--border);
  background: var(--bg-soft);
}
.founder__grid {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 72px;
  align-items: start;
}
.founder__img-wrap { position: relative; }
.founder__img {
  width: 100%; border-radius: 20px; display: block;
  box-shadow: var(--shadow-md);
}
.founder__img-badge {
  position: absolute;
  bottom: 20px; left: 50%; transform: translateX(-50%);
  background: rgba(255,255,255,.92);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,.9);
  border-radius: 50px;
  padding: 8px 20px;
  font-size: 13px; font-weight: 700; color: var(--text);
  display: flex; align-items: center; gap: 6px;
  box-shadow: var(--shadow-sm);
  white-space: nowrap;
}
.founder__img-badge-icon { color: var(--accent); font-size: 14px; }
.founder__name {
  font-size: clamp(30px, 4vw, 48px);
  font-weight: 800; color: var(--text);
  letter-spacing: -.04em; margin: 14px 0 20px;
}
.founder__quote {
  background: var(--accent-light);
  border-left: 3px solid var(--accent);
  border-radius: 0 12px 12px 0;
  padding: 20px 24px;
  font-size: 15px; color: var(--text-body);
  line-height: 1.75; font-style: italic;
  margin-bottom: 24px;
}
.founder__bio {
  font-size: 15px; color: var(--muted);
  line-height: 1.75; margin-bottom: 14px;
}

/* MISSION */
.mission-section {
  padding: 100px 0;
  border-top: 1px solid var(--border);
}
.mission-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; }
.mcard {
  background: #fff; border: 1px solid var(--border);
  border-radius: 20px; padding: 32px;
  box-shadow: var(--shadow-sm);
  transition: box-shadow .2s, transform .2s;
}
.mcard:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.mcard__icon {
  display: inline-flex; align-items: center; justify-content: center;
  width: 46px; height: 46px; border-radius: 12px;
  background: var(--accent-light); color: var(--accent);
  margin-bottom: 18px;
}
.mcard__icon--green { background: #ecfdf5; color: #059669; }
.mcard__title { font-size: 20px; font-weight: 800; color: var(--text); letter-spacing: -.02em; margin-bottom: 12px; }
.mcard__text { font-size: 14px; color: var(--muted); line-height: 1.75; margin-bottom: 16px; }
.mcard__list { list-style: none; display: flex; flex-direction: column; gap: 14px; }
.mcard__list li { display: flex; gap: 12px; font-size: 14px; color: var(--muted); line-height: 1.6; }
.mcard__list strong { color: var(--text); }
.mcard__dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--accent); flex-shrink: 0; margin-top: 7px; }

/* WHY */
.why-section {
  padding: 80px 0 100px;
  background: var(--bg-soft);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}
.why-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: start; }
.why-left__extra { font-size: 14px; color: var(--muted); line-height: 1.7; }
.why-right { display: flex; flex-direction: column; gap: 0; }
.why-card {
  display: flex; align-items: flex-start; gap: 16px;
  padding: 22px 0;
  border-bottom: 1px solid var(--border);
}
.why-card:last-child { border-bottom: none; }
.why-card__icon {
  display: inline-flex; align-items: center; justify-content: center;
  width: 40px; height: 40px; border-radius: 10px;
  background: var(--accent-light); color: var(--accent);
  flex-shrink: 0;
}
.why-card__title { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.why-card__desc { font-size: 14px; color: var(--muted); line-height: 1.65; }

/* CTA */
.about-cta { padding: 80px 0 100px; }
.about-cta__box {
  position: relative;
  background: linear-gradient(135deg, #059669 0%, #10b981 60%, #10b981 100%);
  border-radius: 24px; padding: 80px 56px;
  text-align: center; overflow: hidden;
}
.about-cta__blob {
  position: absolute; width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(255,255,255,.12) 0%, transparent 65%);
  top: 50%; left: 50%; transform: translate(-50%,-50%);
  pointer-events: none;
}
.about-cta__title {
  font-size: clamp(28px, 3.5vw, 46px);
  font-weight: 800; color: #fff;
  letter-spacing: -.04em; line-height: 1.15;
  margin: 12px 0 14px; position: relative;
}
.about-cta__sub { font-size: 15px; color: rgba(255,255,255,.7); margin-bottom: 32px; position: relative; }
.about-cta__btns { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; position: relative; }

@media (max-width: 960px) {
  .gallery-grid { grid-template-columns: 1fr; }
  .gallery-col--left { flex-direction: row; }
  .team-grid { grid-template-columns: 1fr 1fr; }
  .founder__grid { grid-template-columns: 1fr; gap: 40px; }
  .founder__img-wrap { max-width: 360px; }
  .mission-grid { grid-template-columns: 1fr; }
  .why-inner { grid-template-columns: 1fr; gap: 40px; }
}
@media (max-width: 640px) {
  .team-grid { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
  .ah { padding: 140px 0 60px; }
  .ah__stats { gap: 20px; }
  .ah__stat-sep { display: none; }
  .about-cta__box { padding: 48px 24px; }
}
</style>
