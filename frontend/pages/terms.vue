<template>
  <NuxtLayout name="marketing">
    <section class="inner-page-hero py-100">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-10 col-lg-8">

            <div class="section-title mb-60">
              <h2 class="s-52 w-700">Terms of Service</h2>
              <p class="color--grey">Last updated: April 2026</p>
            </div>

            <div class="legal-content">

              <h5 class="s-22 w-700 mt-40">1. Agreement</h5>
              <p>By purchasing any service from CODECV or creating an account on our platform, you agree to these Terms of Service. If you do not agree, do not use our services.</p>
              <p>CODECV is operated by Leopoldo Medeiros, Dublin, Ireland. Contact: <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a> / +353 89 405 0730.</p>

              <h5 class="s-22 w-700 mt-40">2. Services</h5>
              <p>We offer the following services:</p>
              <ul class="simple-list">
                <li class="list-item"><p><strong>Career Accelerator (€99)</strong> — CV writing, LinkedIn optimisation, and cover letter. Delivered within 5 business days of payment confirmation.</p></li>
                <li class="list-item"><p><strong>Laravel + NR Bootcamp (€1,500)</strong> — Cohort-based online training programme, 12–16 weeks, delivered via live video classes.</p></li>
                <li class="list-item"><p><strong>1-on-1 Mentorship (€249/month)</strong> — Two 60-minute video coaching sessions per month plus WhatsApp support. Billed monthly, cancel anytime before the next billing date.</p></li>
              </ul>

              <h5 class="s-22 w-700 mt-40">3. Payment</h5>
              <p>All prices are in euros (EUR) and include VAT where applicable. Payment is processed securely via Stripe. By completing a purchase you authorise the charge to your payment method.</p>
              <p>For monthly Mentorship, you authorise recurring monthly charges until you cancel.</p>

              <h5 class="s-22 w-700 mt-40">4. Refund Policy</h5>
              <ul class="simple-list">
                <li class="list-item"><p><strong>Career Accelerator:</strong> Non-refundable once work has commenced (within 24 hours of payment). If we have not started, a full refund is available within 24 hours of purchase.</p></li>
                <li class="list-item"><p><strong>Laravel + NR Bootcamp:</strong> Non-refundable after the first live class. Before the first class, a full refund is available upon written request.</p></li>
                <li class="list-item"><p><strong>1-on-1 Mentorship:</strong> You may cancel at any time. Cancellation takes effect at the end of the current billing period. No partial refunds for the current month.</p></li>
              </ul>

              <h5 class="s-22 w-700 mt-40">5. Cancellation</h5>
              <p>To cancel a Mentorship subscription, contact us via email or WhatsApp at least 48 hours before your next billing date. We will confirm cancellation within 24 hours.</p>

              <h5 class="s-22 w-700 mt-40">6. Delivery & Scheduling</h5>
              <p>Coaching sessions are scheduled by mutual agreement via WhatsApp or email. We will make reasonable efforts to accommodate your timezone. If you miss a scheduled session without 24-hour notice, that session is forfeited for the month.</p>

              <h5 class="s-22 w-700 mt-40">7. Client Responsibilities</h5>
              <p>To receive the full benefit of our services, you agree to:</p>
              <ul class="simple-list">
                <li class="list-item"><p>Provide accurate and complete information about your background and career goals</p></li>
                <li class="list-item"><p>Respond to communications within a reasonable timeframe</p></li>
                <li class="list-item"><p>Attend scheduled sessions or provide 24 hours notice of cancellation</p></li>
              </ul>

              <h5 class="s-22 w-700 mt-40">8. Intellectual Property</h5>
              <p>CV documents, cover letters, and written materials produced for you are yours to use freely. Bootcamp course materials (slides, recordings, code exercises) are owned by CODECV and may not be redistributed or resold.</p>

              <h5 class="s-22 w-700 mt-40">9. Limitation of Liability</h5>
              <p>CODECV provides career coaching and educational services. We do not guarantee employment outcomes. Our total liability for any claim shall not exceed the amount paid by you for the specific service in question.</p>

              <h5 class="s-22 w-700 mt-40">10. Governing Law</h5>
              <p>These terms are governed by the laws of Ireland. Any disputes shall be subject to the exclusive jurisdiction of the courts of Ireland.</p>

              <h5 class="s-22 w-700 mt-40">11. Changes to These Terms</h5>
              <p>We may update these terms from time to time. We will notify you by email of material changes. Continued use of our services after changes constitutes acceptance.</p>

              <h5 class="s-22 w-700 mt-40">12. Contact</h5>
              <p>For any questions about these terms, contact us at <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a>.</p>

            </div>

          </div>
        </div>
      </div>
    </section>
  </NuxtLayout>
</template>

<script setup>
definePageMeta({ layout: false })
useHead({ title: 'Terms of Service — CODECV' })
</script>
