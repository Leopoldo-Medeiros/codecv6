<template>
  <NuxtLayout name="marketing">
    <section class="inner-page-hero py-100">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-10 col-lg-8">

            <div class="section-title mb-60">
              <h2 class="s-52 w-700">Privacy Policy</h2>
              <p class="color--grey">Last updated: April 2026</p>
            </div>

            <div class="legal-content">

              <h5 class="s-22 w-700 mt-40">1. Who We Are</h5>
              <p>CODECV ("we", "us", "our") is an IT career coaching service operating in Dublin, Ireland. We provide CV writing, mentorship, and professional development services to technology professionals. You can contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a> or via WhatsApp at +353 89 405 0730.</p>

              <h5 class="s-22 w-700 mt-40">2. Data We Collect</h5>
              <p>When you use our platform or services, we may collect:</p>
              <ul class="simple-list">
                <li class="list-item"><p><strong>Account data:</strong> name, email address, password (hashed)</p></li>
                <li class="list-item"><p><strong>Profile data:</strong> professional background, CV content, LinkedIn profile, GitHub, career goals</p></li>
                <li class="list-item"><p><strong>Payment data:</strong> processed by Stripe — we never store card details on our servers</p></li>
                <li class="list-item"><p><strong>Usage data:</strong> pages visited, features used, session information</p></li>
                <li class="list-item"><p><strong>Communications:</strong> messages sent via WhatsApp or email for service delivery</p></li>
              </ul>

              <h5 class="s-22 w-700 mt-40">3. How We Use Your Data</h5>
              <p>We use your data to:</p>
              <ul class="simple-list">
                <li class="list-item"><p>Deliver the services you purchased (CV writing, coaching sessions, learning paths)</p></li>
                <li class="list-item"><p>Manage your account and communicate about your service</p></li>
                <li class="list-item"><p>Process payments via Stripe</p></li>
                <li class="list-item"><p>Improve our platform and personalise your experience</p></li>
                <li class="list-item"><p>Comply with legal obligations (tax, GDPR)</p></li>
              </ul>
              <p>We do <strong>not</strong> sell your personal data to third parties.</p>

              <h5 class="s-22 w-700 mt-40">4. Legal Basis (GDPR)</h5>
              <p>Under GDPR, we process your data on the following grounds:</p>
              <ul class="simple-list">
                <li class="list-item"><p><strong>Contract performance</strong> — to deliver services you have purchased</p></li>
                <li class="list-item"><p><strong>Legitimate interest</strong> — to improve our platform and prevent fraud</p></li>
                <li class="list-item"><p><strong>Legal obligation</strong> — for tax and compliance purposes</p></li>
                <li class="list-item"><p><strong>Consent</strong> — for marketing communications (you may withdraw at any time)</p></li>
              </ul>

              <h5 class="s-22 w-700 mt-40">5. Data Retention</h5>
              <p>We retain your account data for as long as your account is active. After account deletion, we retain anonymised transaction records for 7 years as required by Irish tax law. CV and profile content is deleted within 30 days of account deletion upon request.</p>

              <h5 class="s-22 w-700 mt-40">6. Third-Party Services</h5>
              <p>We use the following third-party processors:</p>
              <ul class="simple-list">
                <li class="list-item"><p><strong>Stripe</strong> — payment processing (privacy.stripe.com)</p></li>
                <li class="list-item"><p><strong>Google Meet / Zoom</strong> — video coaching sessions</p></li>
                <li class="list-item"><p><strong>WhatsApp (Meta)</strong> — client support communications</p></li>
              </ul>

              <h5 class="s-22 w-700 mt-40">7. Your Rights</h5>
              <p>Under GDPR you have the right to:</p>
              <ul class="simple-list">
                <li class="list-item"><p><strong>Access</strong> — request a copy of your personal data</p></li>
                <li class="list-item"><p><strong>Rectification</strong> — correct inaccurate data</p></li>
                <li class="list-item"><p><strong>Erasure</strong> — request deletion of your data ("right to be forgotten")</p></li>
                <li class="list-item"><p><strong>Portability</strong> — receive your data in a machine-readable format</p></li>
                <li class="list-item"><p><strong>Objection</strong> — object to processing based on legitimate interest</p></li>
              </ul>
              <p>To exercise any of these rights, contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a>. We will respond within 30 days.</p>

              <h5 class="s-22 w-700 mt-40">8. Cookies</h5>
              <p>We use only essential cookies required for authentication and session management. We do not use advertising or tracking cookies.</p>

              <h5 class="s-22 w-700 mt-40">9. Data Security</h5>
              <p>We use HTTPS encryption, hashed passwords, and token-based authentication. Payment card data is handled entirely by Stripe and never touches our servers.</p>

              <h5 class="s-22 w-700 mt-40">10. Contact & Complaints</h5>
              <p>For privacy concerns, contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a>. If you are unsatisfied with our response, you have the right to lodge a complaint with the <strong>Data Protection Commission of Ireland</strong> at dataprotection.ie.</p>

            </div>

          </div>
        </div>
      </div>
    </section>
  </NuxtLayout>
</template>

<script setup>
definePageMeta({ layout: false })
useHead({ title: 'Privacy Policy — CODECV' })
</script>
