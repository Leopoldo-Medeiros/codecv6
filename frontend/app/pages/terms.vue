<template>
  <NuxtLayout name="marketing">

    <!-- HERO -->
    <section class="lh">
      <div class="lh__blob" />
      <div class="mkt-container lh__content">
        <span class="section-badge">Legal</span>
        <h1 class="lh__title">Terms of Service</h1>
        <p class="lh__sub">Last updated April 2026</p>
      </div>
    </section>

    <!-- BODY -->
    <section class="legal-body">
      <div class="mkt-container">
        <article class="legal-prose">

          <h2>1. Agreement</h2>
          <p>By purchasing any service from CODECV or creating an account on our platform, you agree to these Terms of Service. If you do not agree, do not use our services.</p>
          <p>CODECV is operated by Leopoldo Medeiros, Dublin, Ireland. Contact: <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a> · +353 89 405 0730.</p>

          <h2>2. Services</h2>
          <p>We offer the following services:</p>
          <ul>
            <li><strong>Career Accelerator (€99)</strong> — CV writing, LinkedIn optimisation, and cover letter. Delivered within 5 business days of payment confirmation.</li>
            <li><strong>Laravel + NR Bootcamp (€1,500)</strong> — Cohort-based online training programme, 12–16 weeks, delivered via live video classes.</li>
            <li><strong>1-on-1 Mentorship (€249/month)</strong> — Two 60-minute video coaching sessions per month plus WhatsApp support. Billed monthly, cancel anytime before the next billing date.</li>
          </ul>

          <h2>3. Payment</h2>
          <p>All prices are in euros (EUR) and include VAT where applicable. Payment is processed securely via Stripe. By completing a purchase you authorise the charge to your payment method.</p>
          <p>For monthly Mentorship, you authorise recurring monthly charges until you cancel.</p>

          <h2>4. Refund Policy</h2>
          <ul>
            <li><strong>Career Accelerator:</strong> Non-refundable once work has commenced (within 24 hours of payment). If we have not started, a full refund is available within 24 hours of purchase.</li>
            <li><strong>Laravel + NR Bootcamp:</strong> Non-refundable after the first live class. Before the first class, a full refund is available upon written request.</li>
            <li><strong>1-on-1 Mentorship:</strong> You may cancel at any time. Cancellation takes effect at the end of the current billing period. No partial refunds for the current month.</li>
          </ul>

          <h2>5. Cancellation</h2>
          <p>To cancel a Mentorship subscription, contact us via email or WhatsApp at least 48 hours before your next billing date. We will confirm cancellation within 24 hours.</p>

          <h2>6. Delivery &amp; Scheduling</h2>
          <p>Coaching sessions are scheduled by mutual agreement via WhatsApp or email. We will make reasonable efforts to accommodate your timezone. If you miss a scheduled session without 24-hour notice, that session is forfeited for the month.</p>

          <h2>7. Client Responsibilities</h2>
          <p>To receive the full benefit of our services, you agree to:</p>
          <ul>
            <li>Provide accurate and complete information about your background and career goals</li>
            <li>Respond to communications within a reasonable timeframe</li>
            <li>Attend scheduled sessions or provide 24 hours notice of cancellation</li>
          </ul>

          <h2>8. Intellectual Property</h2>
          <p>CV documents, cover letters, and written materials produced for you are yours to use freely. Bootcamp course materials (slides, recordings, code exercises) are owned by CODECV and may not be redistributed or resold.</p>

          <h2>9. Limitation of Liability</h2>
          <p>CODECV provides career coaching and educational services. We do not guarantee employment outcomes. Our total liability for any claim shall not exceed the amount paid by you for the specific service in question.</p>

          <h2>10. Governing Law</h2>
          <p>These terms are governed by the laws of Ireland. Any disputes shall be subject to the exclusive jurisdiction of the courts of Ireland.</p>

          <h2>11. Changes to These Terms</h2>
          <p>We may update these terms from time to time. We will notify you by email of material changes. Continued use of our services after changes constitutes acceptance.</p>

          <h2>12. Contact</h2>
          <p>For any questions about these terms, contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a>.</p>

        </article>

        <!-- BOTTOM CARD -->
        <div class="legal-cta">
          <div class="legal-cta__icon">
            <UIcon name="i-heroicons-envelope" style="width:22px;height:22px;" />
          </div>
          <div class="legal-cta__text">
            <h3>Questions about these terms?</h3>
            <p>Reach out anytime — we usually reply within a few hours.</p>
          </div>
          <a href="mailto:codecvinfo@gmail.com" class="mkt-btn mkt-btn--primary">Contact us</a>
        </div>

      </div>
    </section>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })
useSeoMeta({
  title: 'Terms of Service',
  description: 'CODECV Terms of Service — the rules governing our IT career coaching services, payments, and refund policy.',
  ogTitle: 'CODECV — Terms of Service',
  ogDescription: 'The rules governing CODECV career coaching services, payments, and refund policy.',
  robots: 'noindex, follow',
})
</script>

<style scoped>
/* HERO */
.lh {
  position: relative;
  padding: 160px 0 80px;
  text-align: center;
  overflow: hidden;
}
.lh__blob {
  position: absolute; width: 700px; height: 400px;
  background: radial-gradient(ellipse, rgba(5,150,105,.1) 0%, transparent 65%);
  top: -50px; left: 50%; transform: translateX(-50%);
  filter: blur(60px); pointer-events: none;
}
.lh__content { position: relative; z-index: 1; animation: fadeUp .7s ease both; }
@keyframes fadeUp {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.lh__title {
  font-size: clamp(34px, 5vw, 56px);
  font-weight: 800; color: var(--text);
  letter-spacing: -.04em; line-height: 1.1;
  margin: 14px 0 16px;
}
.lh__sub { font-size: 15px; color: var(--muted); }

/* BODY */
.legal-body { padding: 40px 0 100px; }

.legal-prose {
  max-width: 760px;
  margin: 0 auto;
  font-size: 16px;
  line-height: 1.75;
  color: var(--text-body);
}
.legal-prose h2 {
  font-size: 22px;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -.02em;
  margin: 56px 0 16px;
  scroll-margin-top: 100px;
}
.legal-prose h2:first-child { margin-top: 0; }
.legal-prose p { margin: 0 0 16px; }
.legal-prose strong { color: var(--text); font-weight: 700; }
.legal-prose a {
  color: var(--accent);
  text-decoration: none;
  border-bottom: 1px solid rgba(5,150,105,.3);
  transition: border-color .15s, color .15s;
}
.legal-prose a:hover {
  color: var(--accent-hover);
  border-bottom-color: var(--accent);
}
.legal-prose ul {
  margin: 0 0 20px;
  padding: 0;
  list-style: none;
}
.legal-prose li {
  position: relative;
  padding: 0 0 0 24px;
  margin: 0 0 12px;
}
.legal-prose li::before {
  content: '';
  position: absolute;
  left: 4px; top: 12px;
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--accent);
  opacity: .8;
}

/* CONTACT CARD */
.legal-cta {
  max-width: 760px;
  margin: 80px auto 0;
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 24px 28px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 16px;
  box-shadow: var(--shadow-sm);
}
.legal-cta__icon {
  flex-shrink: 0;
  width: 44px; height: 44px;
  border-radius: 12px;
  background: var(--accent-light);
  color: var(--accent);
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.legal-cta__text { flex: 1; min-width: 0; }
.legal-cta__text h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--text);
  letter-spacing: -.01em;
  margin: 0 0 2px;
}
.legal-cta__text p { font-size: 14px; color: var(--muted); margin: 0; }

@media (max-width: 700px) {
  .lh { padding: 140px 0 60px; }
  .legal-prose h2 { font-size: 20px; margin-top: 44px; }
  .legal-cta {
    flex-direction: column;
    text-align: center;
    padding: 28px 24px;
  }
}
</style>
