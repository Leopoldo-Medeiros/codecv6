<template>
  <NuxtLayout name="marketing">

    <section class="fh">
      <div class="fh__blob" />
      <div class="mkt-container fh__content">
        <span class="section-badge">Support</span>
        <h1 class="fh__title">Questions &amp; Answers</h1>
        <p class="fh__sub">Everything you need to know about CODECV and how we can help accelerate your IT career.</p>
      </div>
    </section>

    <section class="faqs-body">
      <div class="mkt-container">
        <div class="faqs-list">
          <div v-for="(faq, i) in faqs" :key="i" class="fitem" @click="active = active === i ? -1 : i">
            <div class="fitem__header">
              <span class="fitem__num">{{ String(i + 1).padStart(2, '0') }}</span>
              <h3 class="fitem__q">{{ faq.question }}</h3>
              <svg :class="['fitem__ico', { 'fitem__ico--open': active === i }]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <Transition name="fa">
              <div v-if="active === i" class="fitem__body">
                <p>{{ faq.answer }}</p>
              </div>
            </Transition>
          </div>
        </div>

        <div class="faqs-cta">
          <div class="faqs-cta__icon">
            <UIcon name="i-heroicons-chat-bubble-left-right" style="width:26px;height:26px;" />
          </div>
          <h3 class="faqs-cta__title">Still have questions?</h3>
          <p class="faqs-cta__sub">Our team is available via WhatsApp, Instagram DM, or email — usually within a few hours.</p>
          <div class="faqs-cta__btns">
            <a href="mailto:codecvinfo@gmail.com" class="mkt-btn mkt-btn--primary mkt-btn--lg">Email us</a>
            <NuxtLink to="/pricing" class="mkt-btn mkt-btn--outline mkt-btn--lg">View pricing</NuxtLink>
          </div>
        </div>
      </div>
    </section>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })
useSeoMeta({
  title: 'Frequently Asked Questions',
  description: 'Answers about CODECV — pricing, consultants, AI tools, and how the structured learning paths work for the Irish IT market.',
  ogTitle: 'CODECV FAQs',
  ogDescription: 'Answers about pricing, consultants, AI tools, and how CODECV helps IT professionals advance careers in Ireland.',
  twitterCard: 'summary_large_image',
})

const active = ref(0)

const faqs = [
  {
    question: 'What is CODECV and how does it work?',
    answer: "CODECV provides consulting for IT professionals — CV writing, LinkedIn profile optimisation, and Cover Letter writing. Our differential is that we provide interview training based on your professional profile, and we accompany the candidate until they pass the probation period.",
  },
  {
    question: 'Can I make an online appointment?',
    answer: "All appointments can be made via WhatsApp, Instagram's DM, or email. We typically respond within a few hours during business hours.",
  },
  {
    question: 'How can I select the best plan?',
    answer: "The first step is to book an appointment with CODECV and explain your background. We'll recommend the best plan based on your current situation and career goals — no pressure, no obligation.",
  },
  {
    question: 'What if I have any questions after the service?',
    answer: "Absolutely fine. Our support is 24/7 — both pre-sale and post-sale. We're committed to your success beyond just delivering a document.",
  },
  {
    question: 'What is included in the Career Accelerator plan?',
    answer: 'The Career Accelerator delivers a complete CV rewrite in English and/or Portuguese, LinkedIn profile optimisation, and a tailored cover letter. Delivery is approximately 5 business days.',
  },
  {
    question: 'Who is the Laravel Bootcamp designed for?',
    answer: "The Laravel + New Relic Bootcamp is designed for junior to mid-level developers who want to break into production-level engineering. You'll learn real Laravel architecture and New Relic observability — skills that get you hired in the Irish market.",
  },
  {
    question: 'How do the 1-on-1 Mentorship sessions work?',
    answer: 'Mentorship sessions are 60-minute video calls twice a month. Between sessions you get WhatsApp support and access to all recorded Bootcamp materials. The plan can be cancelled at any time before the next billing cycle.',
  },
  {
    question: 'Can I get a refund?',
    answer: 'Career Accelerator and Bootcamp enrollment are non-refundable once work has started. Mentorship plans can be cancelled at any time before the next billing cycle with no penalty.',
  },
]

// FAQPage structured data — Google may render this as accordion-style
// rich results in SERP for matching queries.
useSchemaOrg([
  defineWebPage({ '@type': 'FAQPage' }),
  ...faqs.map(faq => defineQuestion({
    name: faq.question,
    acceptedAnswer: faq.answer,
  })),
])
</script>

<style scoped>
.fh {
  position: relative;
  padding: 160px 0 80px;
  text-align: center;
  overflow: hidden;
}
.fh__blob {
  position: absolute; width: 700px; height: 400px;
  background: radial-gradient(ellipse, rgba(5,150,105,.1) 0%, transparent 65%);
  top: -50px; left: 50%; transform: translateX(-50%);
  filter: blur(60px); pointer-events: none;
}
.fh__content { position: relative; z-index: 1; animation: fadeUp .7s ease both; }
@keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.fh__title {
  font-size: clamp(34px, 5vw, 60px);
  font-weight: 800; color: var(--text);
  letter-spacing: -.04em; line-height: 1.1;
  margin: 14px 0 18px;
}
.fh__sub { font-size: 17px; color: var(--muted); max-width: 500px; margin: 0 auto; line-height: 1.7; }

.faqs-body { padding: 60px 0 100px; }
.faqs-list { max-width: 800px; margin: 0 auto 72px; display: flex; flex-direction: column; gap: 10px; }

.fitem {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: box-shadow .2s, border-color .2s;
}
.fitem:hover { box-shadow: var(--shadow-md); border-color: rgba(5,150,105,.2); }
.fitem__header {
  display: flex; align-items: center; gap: 14px;
  padding: 20px 24px;
}
.fitem__num {
  font-size: 12px; font-weight: 800;
  color: var(--accent); opacity: .5;
  flex-shrink: 0; letter-spacing: .04em; min-width: 24px;
}
.fitem__q {
  font-size: 15px; font-weight: 600;
  color: var(--text); flex: 1; line-height: 1.4;
}
.fitem__ico { flex-shrink: 0; color: var(--muted); transition: transform .3s, color .2s; }
.fitem__ico--open { transform: rotate(180deg); color: var(--accent); }
.fitem__body { padding: 0 24px 20px 52px; }
.fitem__body p { font-size: 14px; color: var(--muted); line-height: 1.75; }
.fa-enter-active, .fa-leave-active { transition: all .25s ease; }
.fa-enter-from, .fa-leave-to { opacity: 0; transform: translateY(-6px); }

.faqs-cta {
  max-width: 520px; margin: 0 auto;
  text-align: center;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 20px; padding: 48px 36px;
  box-shadow: var(--shadow-sm);
}
.faqs-cta__icon {
  display: inline-flex; align-items: center; justify-content: center;
  width: 56px; height: 56px; border-radius: 14px;
  background: var(--accent-light); color: var(--accent);
  margin-bottom: 18px;
}
.faqs-cta__title { font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -.02em; margin-bottom: 10px; }
.faqs-cta__sub { font-size: 14px; color: var(--muted); line-height: 1.7; margin-bottom: 24px; }
.faqs-cta__btns { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }

@media (max-width: 640px) {
  .fh { padding: 140px 0 60px; }
  .fitem__body { padding-left: 24px; }
  .faqs-cta { padding: 36px 24px; }
}
</style>
