<template>
  <NuxtLayout name="marketing">

    <!-- HERO -->
    <section class="lh">
      <div class="lh__blob" />
      <div class="mkt-container lh__content">
        <span class="section-badge">Legal</span>
        <h1 class="lh__title">Privacy Policy</h1>
        <p class="lh__sub">Last updated April 2026 · GDPR-compliant</p>
      </div>
    </section>

    <!-- BODY -->
    <section class="legal-body">
      <div class="mkt-container">
        <article class="legal-prose">

          <h2>1. Who We Are</h2>
          <p>CODECV ("we", "us", "our") is an IT career coaching service operating in Dublin, Ireland. We provide CV writing, mentorship, and professional development services to technology professionals. You can contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a> or via WhatsApp at +353 89 405 0730.</p>

          <h2>2. Data We Collect</h2>
          <p>When you use our platform or services, we may collect:</p>
          <ul>
            <li><strong>Account data:</strong> name, email address, password (hashed)</li>
            <li><strong>Profile data:</strong> professional background, CV content, LinkedIn profile, GitHub, career goals</li>
            <li><strong>Payment data:</strong> processed by Stripe — we never store card details on our servers</li>
            <li><strong>Usage data:</strong> pages visited, features used, session information</li>
            <li><strong>Communications:</strong> messages sent via WhatsApp or email for service delivery</li>
          </ul>

          <h2>3. How We Use Your Data</h2>
          <p>We use your data to:</p>
          <ul>
            <li>Deliver the services you purchased (CV writing, coaching sessions, learning paths)</li>
            <li>Manage your account and communicate about your service</li>
            <li>Process payments via Stripe</li>
            <li>Improve our platform and personalise your experience</li>
            <li>Comply with legal obligations (tax, GDPR)</li>
          </ul>
          <p>We do <strong>not</strong> sell your personal data to third parties.</p>

          <h2>4. Legal Basis (GDPR)</h2>
          <p>Under GDPR, we process your data on the following grounds:</p>
          <ul>
            <li><strong>Contract performance</strong> — to deliver services you have purchased</li>
            <li><strong>Legitimate interest</strong> — to improve our platform and prevent fraud</li>
            <li><strong>Legal obligation</strong> — for tax and compliance purposes</li>
            <li><strong>Consent</strong> — for marketing communications (you may withdraw at any time)</li>
          </ul>

          <h2>5. Data Retention</h2>
          <p>We retain your account data for as long as your account is active. After account deletion, we retain anonymised transaction records for 7 years as required by Irish tax law. CV and profile content is deleted within 30 days of account deletion upon request.</p>

          <h2>6. Third-Party Services</h2>
          <p>We use the following third-party processors:</p>
          <ul>
            <li><strong>Stripe</strong> — payment processing (privacy.stripe.com)</li>
            <li><strong>Google Meet / Zoom</strong> — video coaching sessions</li>
            <li><strong>WhatsApp (Meta)</strong> — client support communications</li>
          </ul>

          <h2>7. Your Rights</h2>
          <p>Under GDPR you have the right to:</p>
          <ul>
            <li><strong>Access</strong> — request a copy of your personal data</li>
            <li><strong>Rectification</strong> — correct inaccurate data</li>
            <li><strong>Erasure</strong> — request deletion of your data ("right to be forgotten")</li>
            <li><strong>Portability</strong> — receive your data in a machine-readable format</li>
            <li><strong>Objection</strong> — object to processing based on legitimate interest</li>
          </ul>
          <p>To exercise any of these rights, contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a>. We will respond within 30 days.</p>

          <h2>8. Cookies</h2>
          <p>We use only essential cookies required for authentication and session management. We do not use advertising or tracking cookies.</p>

          <h2>9. Data Security</h2>
          <p>We use HTTPS encryption, hashed passwords, and token-based authentication. Payment card data is handled entirely by Stripe and never touches our servers.</p>

          <h2>10. Contact &amp; Complaints</h2>
          <p>For privacy concerns, contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a>. If you are unsatisfied with our response, you have the right to lodge a complaint with the <strong>Data Protection Commission of Ireland</strong> at <a href="https://dataprotection.ie" target="_blank" rel="noopener">dataprotection.ie</a>.</p>

        </article>

        <!-- BOTTOM CARD -->
        <div class="legal-cta">
          <div class="legal-cta__icon">
            <UIcon name="i-heroicons-shield-check" style="width:22px;height:22px;" />
          </div>
          <div class="legal-cta__text">
            <h3>Privacy concerns or data requests?</h3>
            <p>Email us anytime — we respond within 30 days as required by GDPR.</p>
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
  title: 'Privacy Policy',
  description: 'How CODECV collects, uses, and protects your personal data — GDPR-compliant privacy policy for our IT career coaching platform.',
  ogTitle: 'CODECV — Privacy Policy',
  ogDescription: 'How CODECV collects, uses, and protects your personal data, in compliance with GDPR.',
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
