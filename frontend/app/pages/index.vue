<template>
  <NuxtLayout name="marketing">

    <!-- ─── 1. HERO (splite-style, original chart composition) ── -->
    <section class="hero">
      <div class="hero__decor" aria-hidden="true">
        <div class="blob blob--em"></div>
        <div class="blob blob--ink"></div>
        <div class="grid-bg"></div>
      </div>

      <div class="mkt-container hero__inner">
        <div class="hero__left">
          <span class="pill-badge">
            <span class="pill-badge__dot" aria-hidden="true"></span>
            New cohort starting Summer 2026
          </span>

          <h1 class="h1">
            Advance your
            <span class="h1__em">IT career</span>
            in Ireland.
          </h1>

          <p class="lead">
            Expert coaching, structured learning paths, and curated job
            opportunities — built for IT professionals ready for their
            next role in Dublin and beyond.
          </p>

          <div class="row-cta">
            <NuxtLink to="/register" class="btn btn--em">
              Get Started
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </NuxtLink>
            <NuxtLink to="/pricing" class="btn btn--ghost">View Plans</NuxtLink>
          </div>

          <div class="hero__metrics">
            <div v-for="m in metrics" :key="m.label" class="metric">
              <strong>{{ m.val }}</strong>
              <span>{{ m.label }}</span>
            </div>
          </div>
        </div>

        <div class="hero__right">
          <div class="comp">

            <!-- Subtle background pattern (squiggle + constellation) -->
            <svg class="comp__bg" viewBox="0 0 600 600" aria-hidden="true">
              <g stroke="rgba(16, 185, 129, 0.18)" fill="none" stroke-width="0.8">
                <path d="M0 80 Q 100 50 200 80 T 400 80 T 600 80"/>
                <path d="M0 130 Q 100 100 200 130 T 400 130 T 600 130"/>
                <path d="M0 530 Q 100 500 200 530 T 400 530 T 600 530"/>
                <path d="M0 560 Q 100 530 200 560 T 400 560 T 600 560"/>
              </g>
              <g fill="rgba(15, 23, 42, 0.18)">
                <circle cx="60" cy="40" r="1.4"/><circle cx="120" cy="70" r="1.1"/>
                <circle cx="540" cy="50" r="1.6"/><circle cx="500" cy="100" r="1.1"/>
                <circle cx="80" cy="500" r="1.4"/><circle cx="160" cy="550" r="1.1"/>
                <circle cx="480" cy="540" r="1.6"/><circle cx="540" cy="490" r="1.1"/>
              </g>
            </svg>

            <!-- Multi-line chart -->
            <svg class="comp__chart" viewBox="0 0 500 320" aria-hidden="true">
              <!-- Highlighted vertical band -->
              <rect x="195" y="20" width="60" height="260" fill="rgba(16, 185, 129, 0.12)" rx="2"/>

              <!-- Horizontal gridlines -->
              <g stroke="rgba(15, 23, 42, 0.08)" stroke-width="1">
                <line x1="60" y1="40"  x2="490" y2="40"/>
                <line x1="60" y1="100" x2="490" y2="100"/>
                <line x1="60" y1="160" x2="490" y2="160"/>
                <line x1="60" y1="220" x2="490" y2="220"/>
                <line x1="60" y1="280" x2="490" y2="280"/>
              </g>

              <!-- Y-axis labels -->
              <g fill="rgba(15, 23, 42, 0.45)" font-family="Poppins, sans-serif" font-size="11" font-weight="500" text-anchor="end">
                <text x="50" y="44">80k</text>
                <text x="50" y="104">60k</text>
                <text x="50" y="164">40k</text>
                <text x="50" y="224">20k</text>
                <text x="50" y="284">0</text>
              </g>

              <!-- Chart lines (use bright accents) — peaks pushed higher -->
              <path
                d="M60 250 L 100 220 L 140 150 L 180 230 L 220 70 L 260 25 L 300 110 L 340 70 L 380 180 L 420 80 L 460 30 L 490 120"
                stroke="#10B981" stroke-width="2.4" fill="none" stroke-linejoin="round" stroke-linecap="round"
              />
              <path
                d="M60 265 L 100 245 L 140 195 L 180 240 L 220 150 L 260 200 L 300 140 L 340 170 L 380 200 L 420 150 L 460 195 L 490 170"
                stroke="#F59E0B" stroke-width="2" fill="none" stroke-linejoin="round" stroke-linecap="round"
              />
              <path
                d="M60 278 L 100 268 L 140 258 L 180 272 L 220 252 L 260 262 L 300 252 L 340 248 L 380 252 L 420 242 L 460 248 L 490 240"
                stroke="#34D399" stroke-width="1.8" fill="none" stroke-linejoin="round" stroke-linecap="round"
                opacity="0.7"
              />

              <!-- X-axis labels -->
              <g fill="rgba(15, 23, 42, 0.4)" font-family="Poppins, sans-serif" font-size="10" font-weight="500" text-anchor="middle">
                <text x="140" y="305">Q1 2026</text>
                <text x="225" y="305">Q2 2026</text>
                <text x="345" y="305">Q3 2026</text>
                <text x="455" y="305">Q4 2026</text>
              </g>

              <!-- Clock icon top-center -->
              <g transform="translate(225, 12)">
                <circle r="9" fill="#fff" stroke="rgba(15, 23, 42, 0.3)" stroke-width="1"/>
                <path d="M0 -4 L0 0 L3 2" stroke="rgba(15, 23, 42, 0.7)" stroke-width="1.3" stroke-linecap="round" fill="none"/>
              </g>
            </svg>

            <!-- Developer photo overlaid on the chart -->
            <img
              src="/images/hero-dev.png"
              alt=""
              class="comp__photo"
              loading="eager"
              fetchpriority="high"
            />

            <!-- Hex icon accent (emerald) -->
            <div class="comp__hex" aria-hidden="true">
              <svg viewBox="0 0 80 90">
                <path d="M40 4 L72 22 L72 60 L40 80 L8 60 L8 22 Z" fill="#10B981"/>
                <path d="M40 4 L72 22 L72 60 L40 80 L8 60 L8 22 Z" fill="none" stroke="rgba(255,255,255,0.18)" stroke-width="1"/>
                <!-- bot/cpu glyph -->
                <rect x="29" y="35" width="22" height="16" rx="3" fill="#fff"/>
                <circle cx="35" cy="43" r="1.5" fill="#10B981"/>
                <circle cx="45" cy="43" r="1.5" fill="#10B981"/>
                <line x1="40" y1="29" x2="40" y2="35" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>
                <line x1="36" y1="51" x2="36" y2="55" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
                <line x1="44" y1="51" x2="44" y2="55" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
                <circle cx="40" cy="27" r="1.5" fill="#fff"/>
              </svg>
            </div>

          </div>
        </div>
      </div>
    </section>


    <!-- ─── 2. SERVICE CARDS (NorthWest signature row) ── -->
    <section class="scards">
      <div class="mkt-container">
        <div class="scards__grid">
          <article v-for="c in serviceCards" :key="c.title" class="scard">
            <div class="scard__icon" aria-hidden="true">
              <component :is="c.icon" />
            </div>
            <span class="scard__kicker">{{ c.kicker }}</span>
            <h3 class="scard__title">{{ c.title }}</h3>
            <p class="scard__desc">{{ c.desc }}</p>
            <NuxtLink :to="c.to" class="scard__more">
              read more
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </NuxtLink>
          </article>
        </div>
      </div>
    </section>


    <!-- ─── 2.5 PRACTICE TEASER ──────────────────────── -->
    <section id="try" class="teaser">
      <div class="mkt-container">
        <header class="sec-head">
          <span class="kicker">Try before you sign up</span>
          <h2 class="h2 h2--underline">Solve a real challenge<br>right now, in your browser</h2>
          <p class="sec-sub">
            No account, no setup. Write PHP, run it against real tests, see it pass —
            the same engine our members practice on every day.
          </p>
        </header>

        <div v-if="teasers.length" class="teaser__grid">
          <NuxtLink
            v-for="t in teasers"
            :key="t.slug"
            :to="`/try/${t.slug}`"
            class="teaser__card"
          >
            <span class="teaser__difficulty" :data-level="t.difficulty">{{ t.difficulty }}</span>
            <h3 class="teaser__title">{{ t.title }}</h3>
            <span class="teaser__cta">Try it →</span>
          </NuxtLink>
        </div>
        <p v-else class="teaser__loading">Loading challenges…</p>
      </div>
    </section>


    <!-- ─── 2b. ROADMAP / WAITLIST (demand-sensing) ──── -->
    <WaitlistSection />


    <!-- ─── 3. LOGO CLOUD ────────────────────────────── -->
    <section class="logos">
      <div class="mkt-container">
        <p class="logos__lead">
          Our clients now build at <span>leading tech teams across Ireland</span>
        </p>
        <div class="logos__row">
          <span v-for="l in logos" :key="l" class="logos__item">{{ l }}</span>
        </div>
      </div>
    </section>


    <!-- ─── 4. SPECIALISATIONS (icon grid, "industries") ── -->
    <section class="igrid">
      <div class="mkt-container">
        <header class="sec-head">
          <span class="kicker">Who we coach</span>
          <h2 class="h2 h2--underline">There are Many Corners of IT<br>We Accelerate</h2>
        </header>

        <div class="igrid__grid">
          <div v-for="s in specialisations" :key="s.title" class="igrid__item">
            <div class="igrid__icon" aria-hidden="true">
              <component :is="s.icon" />
            </div>
            <div>
              <h3>{{ s.title }}</h3>
              <p>{{ s.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ─── 5. COUNTERS BAND (emerald) ───────────────── -->
    <section class="counters">
      <div class="counters__pattern" aria-hidden="true"></div>
      <div class="mkt-container counters__grid">
        <div v-for="c in counters" :key="c.label" class="counters__item">
          <strong>{{ c.val }}</strong>
          <span>{{ c.label }}</span>
        </div>
      </div>
    </section>


    <!-- ─── 6. FEATURE TABS (our services) ───────────── -->
    <section class="features">
      <div class="mkt-container">
        <header class="sec-head">
          <span class="kicker">Our services</span>
          <h2 class="h2 h2--underline">Everything you need to<br>land your next role</h2>
          <p class="sec-sub">
            Four pillars working together to move your career forward —
            from CV to offer letter.
          </p>
        </header>

        <div class="tabs">
          <div class="tabs__nav" role="tablist">
            <button
              v-for="(t, i) in tabs"
              :key="t.title"
              type="button"
              class="tab-btn"
              :class="{ 'tab-btn--active': activeTab === i }"
              :aria-selected="activeTab === i"
              role="tab"
              @click="activeTab = i"
            >
              <component :is="t.icon" />
              {{ t.title }}
            </button>
          </div>

          <div v-if="activeTabData" class="tabs__panel">
            <div class="tabs__panel-text">
              <span class="kicker kicker--em">{{ activeTabData.badge }}</span>
              <h3 class="h3">{{ activeTabData.heading }}</h3>
              <p>{{ activeTabData.desc }}</p>
              <ul class="check-list">
                <li v-for="b in activeTabData.bullets" :key="b">
                  <span class="check-list__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none"><path d="M5 13l4 4L19 7" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </span>
                  {{ b }}
                </li>
              </ul>
              <NuxtLink :to="activeTabData.cta" class="btn btn--em-soft">
                Learn more →
              </NuxtLink>
            </div>
            <div class="tabs__panel-visual">
              <img :src="activeTabData.img" :alt="activeTabData.title" />
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ─── 7. TESTIMONIAL BAND (emerald) ────────────── -->
    <!-- pointer events + mouse guard: on touch devices a tap fires a
         synthetic mouseenter with no matching mouseleave, which would
         freeze the auto-rotation permanently -->
    <section class="tband" @pointerenter="onBandPointerEnter" @pointerleave="onBandPointerLeave">
      <div class="tband__pattern" aria-hidden="true"></div>
      <div class="mkt-container tband__inner">
        <span class="kicker kicker--light">Our testimonials</span>
        <h2 class="h2 h2--light h2--underline h2--underline-light">We care about your career</h2>

        <Transition name="qfade" mode="out-in">
          <blockquote :key="quoteIndex" class="tband__quote">
            <p class="tband__text">"{{ activeQuote.text }}"</p>
            <footer class="tband__foot">
              <img :src="activeQuote.img" :alt="activeQuote.author" class="tband__avatar" />
              <strong>{{ activeQuote.author }}</strong>
              <span>{{ activeQuote.role }}</span>
            </footer>
          </blockquote>
        </Transition>

        <div class="tband__nav">
          <button type="button" aria-label="Previous testimonial" @click="prevQuote">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <span class="tband__index">{{ quoteIndex + 1 }} / {{ quotes.length }}</span>
          <button type="button" aria-label="Next testimonial" @click="nextQuote">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </div>
    </section>


    <!-- ─── 8. WORLD MAP ─────────────────────────────── -->
    <section class="globe">
      <div class="mkt-container">
        <header class="sec-head">
          <span class="kicker kicker--em-bright">Global reach</span>
          <h2 class="h2 h2--light h2--underline">Connecting Irish IT talent<br>to teams worldwide</h2>
          <p class="sec-sub sec-sub--light">
            From Dublin to London, Berlin, New York, São Paulo, Tokyo, and
            Sydney — our placements span the globe's leading tech hubs.
          </p>
        </header>

        <div class="globe__wrap">
          <svg :viewBox="WORLD_VIEWBOX" class="globe__svg" aria-hidden="true">
            <!-- Dotted world map — real landmass geography, pre-generated
                 by scripts/generate-world-map.mjs -->
            <g class="globe__dots">
              <circle v-for="(d, i) in mapDots" :key="i" :cx="d[0]" :cy="d[1]" :r="WORLD_DOT_RADIUS" />
            </g>

            <!-- Connection arcs from Dublin to other hubs -->
            <g class="globe__arcs">
              <path
                v-for="(arc, i) in arcs"
                :key="i"
                :d="arc"
                fill="none"
                stroke="#34D399"
                stroke-width="1.4"
                stroke-dasharray="5 5"
                stroke-linecap="round"
              />
            </g>

            <!-- City markers -->
            <g class="globe__markers">
              <g v-for="city in cities" :key="city.name" :transform="`translate(${city.x},${city.y})`">
                <circle r="13" fill="rgba(52, 211, 153, 0.15)" />
                <circle r="6" fill="rgba(52, 211, 153, 0.45)" />
                <circle r="3" fill="#34D399" />
              </g>
            </g>

            <!-- City labels (per-city offsets keep the EU cluster readable) -->
            <g class="globe__labels">
              <text
                v-for="city in cities"
                :key="city.name + '-l'"
                :x="city.x + city.labelDx"
                :y="city.y + city.labelDy"
                :text-anchor="city.labelDx < 0 ? 'end' : 'start'"
              >{{ city.name }}</text>
            </g>
          </svg>

          <div class="globe__stats">
            <div v-for="g in globeStats" :key="g.label" class="globe__stat">
              <strong>{{ g.val }}</strong>
              <span>{{ g.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ─── 9. FAQ ───────────────────────────────────── -->
    <section class="faq">
      <div class="mkt-container faq__grid">
        <div class="faq__head">
          <span class="kicker">FAQ</span>
          <h2 class="h2 h2--underline h2--underline-left">Got questions?<br>We've got answers.</h2>
          <p>
            Can't find what you're looking for?
            <a href="mailto:codecvinfo@gmail.com">Reach out directly →</a>
          </p>
        </div>

        <div class="faq__list">
          <div
            v-for="(f, i) in faqs"
            :key="f.q"
            class="faq__item"
            :class="{ 'faq__item--open': openFaq === i }"
          >
            <button
              type="button"
              class="faq__q"
              :aria-expanded="openFaq === i"
              @click="toggleFaq(i)"
            >
              <span>{{ f.q }}</span>
              <span class="faq__chev" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
            </button>
            <div v-show="openFaq === i" class="faq__a">{{ f.a }}</div>
          </div>
        </div>
      </div>
    </section>


    <!-- ─── 10. FINAL CTA ────────────────────────────── -->
    <section class="cta-final">
      <div class="mkt-container cta-final__inner">
        <span class="kicker kicker--em-bright">Ready to start?</span>
        <h2 class="h2 h2--light">Advance your IT career today.</h2>
        <p>Join 200+ IT professionals who've found their next role with CODECV.</p>
        <div class="row-cta row-cta--center">
          <NuxtLink to="/register" class="btn btn--em btn--lg">Get Started Free</NuxtLink>
          <NuxtLink to="/pricing" class="btn btn--ghost-light">View Plans →</NuxtLink>
        </div>
      </div>
    </section>

  </NuxtLayout>
</template>

<script lang="ts">
import { WORLD_CITIES, WORLD_DOTS, WORLD_DOT_RADIUS, WORLD_VIEWBOX } from '~/utils/world-map-data'

/* Connection arcs from Dublin (first city) to every other hub — module
   scope: the data is static, so it is computed once per app lifetime. */
const HOME = WORLD_CITIES[0]!
const MAP_ARCS = WORLD_CITIES.slice(1).map((c) => {
  const cx = (HOME.x + c.x) / 2
  const cy = Math.min(HOME.y, c.y) - Math.abs(c.x - HOME.x) * 0.2
  return `M${HOME.x} ${HOME.y} Q ${cx} ${cy} ${c.x} ${c.y}`
})
</script>

<script setup lang="ts">
import { h } from 'vue'

definePageMeta({ layout: false })

useSeoMeta({
  title: 'IT Career Coaching Platform for Ireland',
  description: 'CODECV — CV analysis, LinkedIn optimisation, structured learning paths, and 1-on-1 coaching for IT professionals in Ireland.',
  ogTitle: 'CODECV — IT Career Coaching for Ireland',
  ogDescription: 'CV analysis, LinkedIn optimisation, structured learning paths, and 1-on-1 coaching for IT professionals in Ireland.',
  twitterCard: 'summary_large_image',
})

/* === DATA === */
const metrics = [
  { val: '200+', label: 'Professionals coached' },
  { val: '92%',  label: 'Placement rate' },
  { val: '4.9★', label: 'Client rating' },
]

const logos = ['Stripe', 'Workday', 'Intercom', 'HubSpot', 'Salesforce', 'LinkedIn']

/* === Icons (stroke follows currentColor so CSS owns the palette) === */
const iw = { viewBox: '0 0 24 24', width: 26, height: 26, fill: 'none' }
const sw = { stroke: 'currentColor', 'stroke-width': 1.7, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' } as const

const IconDoc = () => h('svg', iw, [
  h('path', { d: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z', ...sw }),
  h('path', { d: 'M14 2v6h6M8 13h8M8 17h5', ...sw }),
])
const IconChat = () => h('svg', iw, [
  h('path', { d: 'M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z', ...sw }),
])
const IconRoad = () => h('svg', iw, [
  h('path', { d: 'M3 6h10M3 12h14M3 18h7', ...sw }),
  h('circle', { cx: 19, cy: 6, r: 2, ...sw }),
])
const IconBriefcase = () => h('svg', iw, [
  h('rect', { x: 3, y: 7, width: 18, height: 13, rx: 2, ...sw }),
  h('path', { d: 'M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18', ...sw }),
])
const IconBolt = () => h('svg', iw, [
  h('path', { d: 'M13 2L3 14h7l-1 8 10-12h-7l1-8z', ...sw }),
])
const IconShield = () => h('svg', iw, [
  h('path', { d: 'M12 2l8 4v6c0 5-3.5 9.4-8 10-4.5-.6-8-5-8-10V6l8-4z', ...sw }),
  h('path', { d: 'M9 12l2 2 4-4', ...sw }),
])
const IconCompass = () => h('svg', iw, [
  h('circle', { cx: 12, cy: 12, r: 9, ...sw }),
  h('path', { d: 'M16 8l-2 6-6 2 2-6 6-2z', ...sw }),
])
const IconCode = () => h('svg', iw, [
  h('path', { d: 'M8 6l-6 6 6 6M16 6l6 6-6 6M13 4l-2 16', ...sw }),
])
const IconCloud = () => h('svg', iw, [
  h('path', { d: 'M18 10a5 5 0 0 0-9.6-1.9A4.5 4.5 0 0 0 6.5 17H18a3.5 3.5 0 0 0 0-7z', ...sw }),
])
const IconChart = () => h('svg', iw, [
  h('path', { d: 'M4 20V10M10 20V4M16 20v-8M21 20H3', ...sw }),
])
const IconUsers = () => h('svg', iw, [
  h('circle', { cx: 9, cy: 8, r: 3.5, ...sw }),
  h('path', { d: 'M2.5 20a6.5 6.5 0 0 1 13 0M16 5a3.5 3.5 0 0 1 0 7M21.5 20a6.5 6.5 0 0 0-4.5-6.2', ...sw }),
])
const IconBug = () => h('svg', iw, [
  h('circle', { cx: 12, cy: 13, r: 5, ...sw }),
  h('path', { d: 'M12 8V5M8.5 9L6 6.5M15.5 9L18 6.5M7 13H4M20 13h-3M8.5 17L6 19.5M15.5 17L18 19.5', ...sw }),
])

/* === Service cards (NorthWest-style row) === */
const serviceCards = [
  { icon: IconBolt,    kicker: 'Career Service',   title: 'AI-powered CV review', desc: 'Instant ATS scoring and keyword analysis tuned for the Irish tech market.', to: '/about' },
  { icon: IconShield,  kicker: 'Coaching Service', title: 'Verified placements',  desc: 'Every coach we work with has placed engineers at Dublin firms in the last 12 months.', to: '/about' },
  { icon: IconCompass, kicker: 'Guidance Service', title: 'Career direction',     desc: 'Personalised guidance from where you are now to where you want to be in 12 months.', to: '/about' },
]

/* === Specialisations (icon grid) === */
const specialisations = [
  { icon: IconCode,   title: 'Software Engineering',   desc: 'Backend, full-stack, and platform engineers levelling up to senior roles.' },
  { icon: IconCloud,  title: 'DevOps & Cloud',         desc: 'AWS, Kubernetes, and platform teams — the most in-demand skills in Dublin.' },
  { icon: IconChart,  title: 'Data & Analytics',       desc: 'Data engineers and analysts moving into high-impact product teams.' },
  { icon: IconBug,    title: 'QA & Testing',           desc: 'Automation engineers making the jump from manual testing to SDET roles.' },
  { icon: IconRoad,   title: 'Career Switchers',       desc: 'Professionals transitioning into tech through structured learning paths.' },
  { icon: IconUsers,  title: 'Engineering Leadership', desc: 'Senior engineers preparing for tech lead and engineering manager roles.' },
]

/* === Counters band === */
const counters = [
  { val: '200+', label: 'Professionals Coached' },
  { val: '92%',  label: 'Placement Rate' },
  { val: '12+',  label: 'Countries Served' },
  { val: '4.9',  label: 'Client Rating' },
]

/* === Feature tabs === */
const tabs = [
  {
    title: 'CV Writing',
    icon: IconDoc,
    badge: 'PILLAR 01',
    heading: 'CVs written for the Irish IT market',
    desc: 'Bespoke CVs crafted to pass ATS systems and impress hiring managers at the firms you actually want to work for.',
    bullets: ['ATS-optimised keyword strategy', 'Tailored for Irish recruiters', 'Two rounds of revisions included'],
    cta: '/about',
    img: 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?auto=format&fit=crop&w=900&q=88',
  },
  {
    title: '1-on-1 Coaching',
    icon: IconChat,
    badge: 'PILLAR 02',
    heading: 'Senior consultants in your corner',
    desc: 'Weekly sessions with consultants who know the Dublin hiring landscape inside out. Mock interviews, salary strategy, the works.',
    bullets: ['Weekly 1-on-1 sessions', 'Mock technical interviews', 'Salary negotiation playbook'],
    cta: '/about',
    img: 'https://images.unsplash.com/photo-1573164713988-8665fc963095?auto=format&fit=crop&w=900&q=88',
  },
  {
    title: 'Learning Paths',
    icon: IconRoad,
    badge: 'PILLAR 03',
    heading: 'Structured roadmaps, milestone-tracked',
    desc: 'From junior to cloud architect — curated courses, real exercises, and weekly milestones so you actually finish.',
    bullets: ['Curated courses + exercises', 'Weekly milestone tracking', 'Coding challenges with feedback'],
    cta: '/about',
    img: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=900&q=88',
  },
  {
    title: 'Job Matching',
    icon: IconBriefcase,
    badge: 'PILLAR 04',
    heading: 'Direct intros to hiring teams',
    desc: 'Curated roles matched to your stack, with direct introductions to engineering managers across Dublin.',
    bullets: ['Curated weekly job feed', 'Direct intros to hiring managers', 'Offer support through to signing'],
    cta: '/about',
    img: 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=88',
  },
]
const activeTab = ref(0)
const activeTabData = computed(() => tabs[activeTab.value])

/* === Testimonials — rotating quote on the emerald band === */
const quotes = [
  { text: 'CODECV helped me land a senior developer role in Dublin within 3 months. The CV rewrite was a complete game-changer.', author: 'Tomasz K.', role: 'Senior Java Developer', img: '/images/review-author-1.jpg' },
  { text: 'I was stuck for years. CODECV got me into a tech lead role in four months — start to finish.', author: 'Ciara M.', role: 'Tech Lead', img: '/images/review-author-4.jpg' },
  { text: 'Worth every euro. My CV went from getting rejected to getting 7 callbacks in the first week.', author: 'Patrick D.', role: 'Backend Engineer', img: '/images/review-author-3.jpg' },
  { text: 'Mock interviews with senior engineers were brutal but exactly what I needed before going for FAANG.', author: 'Daniel R.', role: 'Software Engineer · Google', img: '/images/review-author-1.jpg' },
  { text: 'The structured learning path gave me a clear roadmap. I went from junior to mid-level engineer in 6 months.', author: 'Maria S.', role: 'Software Engineer', img: '/images/review-author-2.jpg' },
  { text: 'Coaches who actually work in the Irish market. Their advice was specific, not generic LinkedIn fluff.', author: 'Aoife N.', role: 'Data Engineer', img: '/images/review-author-4.jpg' },
  { text: 'My consultant knew exactly which Dublin firms were hiring and helped me tailor every application.', author: 'Arun P.', role: 'Cloud Engineer', img: '/images/review-author-3.jpg' },
  { text: 'The platform tracks every step. I always knew exactly what to do next, no more drifting.', author: 'Sofia L.', role: 'Frontend Engineer', img: '/images/review-author-2.jpg' },
  { text: 'The salary negotiation playbook alone paid for the entire programme three times over.', author: 'Liam O.', role: 'Senior DevOps Engineer', img: '/images/review-author-1.jpg' },
]
const quoteIndex = ref(0)
const activeQuote = computed(() => quotes[quoteIndex.value]!)
function nextQuote() { quoteIndex.value = (quoteIndex.value + 1) % quotes.length }
function prevQuote() { quoteIndex.value = (quoteIndex.value - 1 + quotes.length) % quotes.length }

let quoteTimer: ReturnType<typeof setInterval> | null = null
function resumeQuotes() {
  if (quoteTimer) return
  quoteTimer = setInterval(nextQuote, 7000)
}
function pauseQuotes() {
  if (quoteTimer) { clearInterval(quoteTimer); quoteTimer = null }
}
function onBandPointerEnter(e: PointerEvent) { if (e.pointerType === 'mouse') pauseQuotes() }
function onBandPointerLeave(e: PointerEvent) { if (e.pointerType === 'mouse') resumeQuotes() }

onMounted(resumeQuotes)
onUnmounted(pauseQuotes)

/* === World map (real geography — see app/utils/world-map-data.ts) === */
const cities = WORLD_CITIES
const arcs = MAP_ARCS
const mapDots = WORLD_DOTS

const globeStats = [
  { val: '12+', label: 'Countries served' },
  { val: '200+', label: 'Placements globally' },
  { val: '24/7', label: 'Consultant support' },
]

/* === FAQ === */
const faqs = [
  { q: 'Who is CODECV for?', a: 'IT professionals at any stage — junior, mid, senior — looking to advance their careers in the Irish tech market. Most clients are software engineers, DevOps, cloud, and data professionals based in or moving to Ireland.' },
  { q: 'How long does the programme take?', a: 'It depends on your goal. A CV rewrite is a 2-week sprint. Landing a new role typically takes 3–6 months from kickoff. Learning paths can run 6–12 months depending on the track.' },
  { q: 'Do you guarantee a job placement?', a: 'No reputable coaching service can guarantee a placement, and we don\'t pretend otherwise. What we do guarantee: a market-ready CV, direct introductions to hiring managers, and weekly support until you sign an offer.' },
  { q: 'How much does it cost?', a: 'Plans start at €299 for a CV rewrite. Full coaching programmes (CV + 1-on-1 + learning path + job matching) run €1,499 for 3 months. See the Pricing page for the current breakdown.' },
  { q: 'Can I cancel anytime?', a: 'Yes. All plans are month-to-month with no minimum commitment. Cancel from your dashboard and you keep access through the end of your billing cycle.' },
  { q: 'Do you work with non-EU candidates?', a: 'Yes — we have experience guiding candidates through Irish Critical Skills work permits, the Stamp 1G post-study path, and inter-company transfers. Visa specialist included on consultant calls when needed.' },
]
const openFaq = ref<number | null>(0)
function toggleFaq(i: number) { openFaq.value = openFaq.value === i ? null : i }

/* === Practice teaser (F2) — fetched client-side; the homepage is
   prerendered, so this hydrates after load with the current teaser set === */
interface TeaserSummary { title: string, slug: string, difficulty: string }
const teasers = ref<TeaserSummary[]>([])

onMounted(async () => {
  try {
    const response = await $fetch<{ data: TeaserSummary[] }>(
      '/api/public/challenges/teaser',
      { baseURL: useRuntimeConfig().public.apiBase as string },
    )
    teasers.value = response.data
  } catch {
    teasers.value = []
  }
})
</script>

<style scoped>
/* ╔══════════════════════════════════════════════════════════════════╗
   ║  DESIGN TOKENS — defined on every section root so they cascade.  ║
   ║  (Vue scoped CSS rewrites :root so we can't use it.)             ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.hero, .scards, .logos, .igrid, .counters, .features, .tband, .globe, .faq, .cta-final {
  /* Color */
  --em:       #059669;
  --em-2:     #047857;
  --em-3:     #064E3B;
  --em-soft:  #D1FAE5;
  --em-ring:  #A7F3D0;
  --em-bright:#34D399;
  --ink:      #0F172A;
  --ink-2:    #1E293B;
  --slate:    #475569;
  --muted:    #64748B;
  --muted-2:  #94A3B8;
  --border:   #E2E8F0;
  --border-2: #CBD5E1;
  --bg:       #FFFFFF;
  --bg-soft:  #F8FAFC;
  --bg-mute:  #F1F5F9;
  /* Spacing */
  --pad-sec:  clamp(72px, 9vw, 112px);
  /* Radius — corporate: subtle, not pill */
  --r-sm:     4px;
  --r-md:     6px;
  --r-lg:     8px;
  /* Shadow */
  --s-sm:     0 2px 10px rgba(15, 23, 42, 0.06);
  --s-md:     0 14px 40px rgba(15, 23, 42, 0.10);
  --s-lg:     0 20px 50px rgba(15, 23, 42, 0.14);

  font-family: var(--ff, 'Poppins', sans-serif);
  color: #17212B;
}

/* ── TYPE SYSTEM ───────────────────────────────────────────────────── */
.h1, .h2, .h3 {
  font-weight: 700;
  letter-spacing: -0.015em;
  color: var(--ink);
  margin: 0;
}
.h1 {
  font-size: clamp(2.3rem, 4.2vw, 3.6rem);
  line-height: 1.12;
}
.h1__em { color: var(--em); }
.h2 {
  font-size: clamp(1.7rem, 3.1vw, 2.4rem);
  line-height: 1.22;
}
.h2--light { color: #fff; }
/* NorthWest motif: short green underline bar below the heading */
.h2--underline {
  position: relative;
  padding-bottom: 22px;
}
.h2--underline::after {
  content: '';
  position: absolute;
  left: 50%; bottom: 0;
  transform: translateX(-50%);
  width: 54px; height: 3px;
  background: var(--em);
}
.h2--underline-light::after { background: #fff; }
.h2--underline-left { padding-bottom: 22px; }
.h2--underline-left::after { left: 0; transform: none; }
.h3 {
  font-size: clamp(1.25rem, 1.9vw, 1.55rem);
  font-weight: 700;
  line-height: 1.25;
}
.lead {
  font-size: 1.02rem;
  line-height: 1.78;
  color: var(--slate);
  margin: 0;
  max-width: 470px;
  font-weight: 400;
}
.kicker {
  display: inline-block;
  font-size: 0.76rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  color: var(--muted);
  text-transform: uppercase;
  margin-bottom: 12px;
}
.kicker--em { color: var(--em); }
.kicker--em-bright { color: var(--em-ring); }
.kicker--light { color: rgba(255, 255, 255, 0.8); }
.sec-sub {
  font-size: 0.98rem;
  line-height: 1.75;
  color: var(--muted);
  max-width: 560px;
  margin: 20px auto 0;
}
.sec-sub--light { color: rgba(255, 255, 255, 0.65); }

.sec-head { text-align: center; margin-bottom: 52px; }
.sec-head .h2 { margin-left: auto; margin-right: auto; }

/* ── BUTTONS — rectangular, uppercase, consulting style ────────────── */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 28px;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  border-radius: 3px;
  text-decoration: none;
  border: 2px solid transparent;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, transform 0.18s, box-shadow 0.2s, border-color 0.2s;
  white-space: nowrap;
  font-family: inherit;
}
.btn--em { background: var(--em); color: #fff; border-color: var(--em); }
.btn--em:hover { background: var(--em-2); border-color: var(--em-2); transform: translateY(-2px); box-shadow: 0 10px 24px rgba(5, 150, 105, 0.3); }
.btn--ghost {
  background: transparent;
  color: var(--ink);
  border-color: var(--border-2);
}
.btn--ghost:hover { border-color: var(--ink); background: var(--bg-soft); }
.btn--ghost-light {
  background: transparent;
  color: rgba(255, 255, 255, 0.85);
  border-color: rgba(255, 255, 255, 0.3);
}
.btn--ghost-light:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.6); }
.btn--em-soft {
  background: var(--em-soft);
  color: var(--em-2);
}
.btn--em-soft:hover { background: var(--em-ring); }
.btn--lg { padding: 16px 36px; font-size: 0.86rem; }

.row-cta {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
  margin-bottom: 44px;
}
.row-cta--center { justify-content: center; margin-bottom: 0; }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  1. HERO (original splite-style composition)                     ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.hero {
  position: relative;
  padding: clamp(48px, 6vw, 88px) 0 40px;
  background: var(--bg);
  overflow: hidden;
}
.hero__decor { position: absolute; inset: 0; pointer-events: none; z-index: 0; }
.blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.5;
}
.blob--em { width: 480px; height: 480px; background: var(--em-ring); top: -100px; right: -120px; }
.blob--ink { width: 360px; height: 360px; background: var(--bg-mute); bottom: -160px; left: -120px; opacity: 1; }
.grid-bg {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(var(--border) 1px, transparent 1px),
    linear-gradient(90deg, var(--border) 1px, transparent 1px);
  background-size: 48px 48px;
  mask-image: radial-gradient(ellipse at center, #000 30%, transparent 70%);
  opacity: 0.35;
}
.hero__inner {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: 1.05fr 1fr;
  gap: 64px;
  align-items: center;
}

.pill-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--em-soft);
  color: var(--em-3);
  font-size: 0.78rem;
  font-weight: 600;
  padding: 8px 14px;
  border-radius: 3px;
  margin-bottom: 28px;
}
.pill-badge__dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--em);
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.22);
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.22); }
  50%      { box-shadow: 0 0 0 6px rgba(5, 150, 105, 0.05); }
}

.hero .h1 { margin-bottom: 22px; }
.hero .lead { margin-bottom: 32px; }

.hero__metrics {
  display: flex;
  gap: clamp(24px, 4vw, 48px);
  padding-top: 28px;
  border-top: 1px solid var(--border);
}
.metric { display: flex; flex-direction: column; gap: 4px; }
.metric strong {
  font-size: 1.55rem;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.02em;
  line-height: 1;
}
.metric span {
  font-size: 0.74rem;
  color: var(--muted);
  font-weight: 500;
}

/* Hero right column — chart composition */
.hero__right {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 640px;
}

.comp {
  position: relative;
  width: 100%;
  max-width: 700px;
  aspect-ratio: 1 / 1;
  background: transparent;
  overflow: visible;
}

/* Subtle background pattern (squiggles + constellation) */
.comp__bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

/* Chart — sits behind everything else, taller so peaks reach above the dev */
.comp__chart {
  position: absolute;
  top: -12%;
  left: -3%;
  right: -3%;
  width: 106%;
  height: 100%;
  z-index: 1;
}

/* Developer photo overlaid */
.comp__photo {
  position: absolute;
  bottom: -6%;
  right: -4%;
  height: 124%;
  width: auto;
  max-width: 92%;
  object-fit: contain;
  object-position: bottom right;
  z-index: 2;
  filter: drop-shadow(0 18px 32px rgba(15, 23, 42, 0.15));
}

/* Hex icon accent */
.comp__hex {
  position: absolute;
  top: 34%;
  right: 6%;
  width: 60px;
  height: auto;
  z-index: 3;
  filter: drop-shadow(0 8px 24px rgba(16, 185, 129, 0.35));
}
.comp__hex svg { width: 100%; height: auto; display: block; }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  2. SERVICE CARDS (NorthWest row)                                 ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.scards {
  position: relative;
  z-index: 2;
  padding: 24px 0 var(--pad-sec);
  background: var(--bg);
}
.scards__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
.scard {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  box-shadow: var(--s-sm);
  padding: 36px 32px 32px;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.scard:hover { transform: translateY(-5px); box-shadow: var(--s-md); }
.scard__icon {
  display: inline-flex;
  color: var(--ink);
  margin-bottom: 22px;
  transition: color 0.25s;
}
.scard:hover .scard__icon { color: var(--em); }
.scard__kicker {
  display: block;
  font-size: 0.74rem;
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 6px;
}
.scard__title {
  font-size: 1.14rem;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 12px;
  transition: color 0.25s;
}
.scard:hover .scard__title { color: var(--em); }
.scard__desc {
  font-size: 0.92rem;
  line-height: 1.72;
  color: var(--muted);
  margin-bottom: 22px;
}
.scard__more {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: lowercase;
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s, gap 0.2s;
}
.scard__more svg { color: var(--em); }
.scard__more:hover { color: var(--em); gap: 12px; }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  2.5 PRACTICE TEASER                                             ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.teaser { padding: var(--pad-sec) 0; background: var(--bg-soft); }
.teaser__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.teaser__card {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 12px;
  padding: 24px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  box-shadow: var(--s-sm);
  text-decoration: none;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.teaser__card:hover { transform: translateY(-4px); box-shadow: var(--s-md); border-color: var(--em-ring); }
.teaser__difficulty {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 3px 10px;
  border-radius: 3px;
  background: var(--em-soft);
  color: var(--em-2);
}
.teaser__difficulty[data-level="advanced"],
.teaser__difficulty[data-level="expert"] {
  background: rgba(168, 113, 10, 0.1);
  color: #A8710A;
}
.teaser__title {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--ink);
  margin: 0;
  flex: 1;
}
.teaser__cta { font-size: 0.9rem; font-weight: 600; color: var(--em); }
.teaser__loading { text-align: center; color: var(--muted); }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  3. LOGO CLOUD                                                    ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.logos {
  padding: 52px 0;
  background: var(--bg-soft);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}
.logos__lead {
  text-align: center;
  font-size: 0.88rem;
  color: var(--muted);
  margin: 0 0 28px;
  font-weight: 500;
}
.logos__lead span { color: var(--ink); font-weight: 600; }
.logos__row {
  display: flex;
  align-items: center;
  justify-content: space-around;
  gap: clamp(20px, 3vw, 48px);
  flex-wrap: wrap;
}
.logos__item {
  font-size: 1.35rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: var(--muted-2);
  opacity: 0.75;
  transition: opacity 0.2s, color 0.2s;
}
.logos__item:hover { opacity: 1; color: var(--ink); }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  4. SPECIALISATIONS (icon grid)                                   ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.igrid { padding: var(--pad-sec) 0; background: var(--bg); }
.igrid__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: clamp(36px, 4vw, 56px) clamp(32px, 4vw, 56px);
}
.igrid__item {
  display: flex;
  align-items: flex-start;
  gap: 20px;
}
.igrid__icon {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 58px; height: 58px;
  border: 1px solid var(--border-2);
  border-radius: var(--r-md);
  color: var(--ink);
  transition: background 0.25s, color 0.25s, border-color 0.25s, transform 0.25s;
}
.igrid__item:hover .igrid__icon {
  background: var(--em);
  border-color: var(--em);
  color: #fff;
  transform: translateY(-3px);
}
.igrid__item h3 {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--ink);
  margin: 4px 0 8px;
  line-height: 1.35;
}
.igrid__item p {
  font-size: 0.9rem;
  line-height: 1.7;
  color: var(--muted);
  margin: 0;
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  5. COUNTERS BAND (emerald)                                       ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.counters {
  position: relative;
  background: var(--em);
  padding: clamp(52px, 6vw, 72px) 0;
  overflow: hidden;
}
.counters__pattern {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.14) 1.5px, transparent 1.5px);
  background-size: 22px 22px;
  mask-image: linear-gradient(90deg, transparent, #000 40%, #000 60%, transparent);
  -webkit-mask-image: linear-gradient(90deg, transparent, #000 40%, #000 60%, transparent);
  pointer-events: none;
}
.counters__grid {
  position: relative;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
  text-align: center;
}
.counters__item strong {
  display: block;
  font-size: clamp(2.1rem, 3.6vw, 2.9rem);
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.02em;
  line-height: 1;
  margin-bottom: 10px;
}
.counters__item span {
  font-size: 0.84rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.85);
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  6. FEATURE TABS                                                  ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.features {
  padding: var(--pad-sec) 0;
  background: var(--bg);
}

.tabs__nav {
  display: flex;
  justify-content: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 44px;
}
.tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--bg-soft);
  border: 1px solid var(--border);
  font-size: 0.86rem;
  font-weight: 600;
  color: var(--slate);
  padding: 11px 20px;
  border-radius: 3px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, border-color 0.2s;
  font-family: inherit;
}
.tab-btn:hover { color: var(--ink); border-color: var(--border-2); }
.tab-btn--active,
.tab-btn--active:hover {
  background: var(--em);
  border-color: var(--em);
  color: #fff;
}
.tab-btn--active :deep(svg path),
.tab-btn--active :deep(svg circle),
.tab-btn--active :deep(svg rect) { stroke: #fff; }

.tabs__panel {
  display: grid;
  grid-template-columns: 1fr 1.1fr;
  gap: 56px;
  align-items: center;
  background: var(--bg-soft);
  border: 1px solid var(--border);
  border-radius: var(--r-lg);
  padding: clamp(28px, 4vw, 56px);
}
.tabs__panel-text > p {
  font-size: 0.98rem;
  line-height: 1.75;
  color: var(--slate);
  margin: 16px 0 24px;
}
.tabs__panel-text .kicker { margin-bottom: 10px; }
.check-list {
  list-style: none;
  padding: 0;
  margin: 0 0 28px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.check-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 0.92rem;
  color: var(--ink-2);
  font-weight: 500;
}
.check-list__icon {
  width: 20px;
  height: 20px;
  background: var(--em-soft);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}
.tabs__panel-visual img {
  width: 100%;
  border-radius: var(--r-md);
  display: block;
  aspect-ratio: 4 / 3;
  object-fit: cover;
  box-shadow: var(--s-md);
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  7. TESTIMONIAL BAND (emerald)                                    ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.tband {
  position: relative;
  background: var(--em);
  padding: clamp(64px, 8vw, 96px) 0;
  overflow: hidden;
}
.tband__pattern {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.5px, transparent 1.5px);
  background-size: 22px 22px;
  mask-image: linear-gradient(180deg, #000, transparent 55%);
  -webkit-mask-image: linear-gradient(180deg, #000, transparent 55%);
  pointer-events: none;
}
.tband__inner {
  position: relative;
  text-align: center;
  max-width: 820px;
}
.tband .h2 { margin-bottom: 40px; }

.tband__quote { margin: 0; }
.tband__text {
  font-size: clamp(1.1rem, 2vw, 1.4rem);
  font-weight: 500;
  line-height: 1.65;
  color: #fff;
  margin: 0 auto 30px;
  max-width: 720px;
}
.tband__foot {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.tband__avatar {
  width: 56px; height: 56px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.6);
  margin-bottom: 10px;
}
.tband__foot strong { color: #fff; font-size: 0.98rem; font-weight: 700; }
.tband__foot span { color: rgba(255, 255, 255, 0.75); font-size: 0.82rem; }

.tband__nav {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  margin-top: 34px;
}
.tband__nav button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px; height: 42px;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.45);
  border-radius: 3px;
  color: #fff;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
}
.tband__nav button:hover { background: rgba(255, 255, 255, 0.14); border-color: #fff; }
.tband__index {
  font-size: 0.82rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.85);
  letter-spacing: 0.08em;
}

.qfade-enter-active, .qfade-leave-active { transition: opacity 0.3s ease, transform 0.3s ease; }
.qfade-enter-from { opacity: 0; transform: translateY(12px); }
.qfade-leave-to { opacity: 0; transform: translateY(-8px); }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  8. WORLD MAP                                                     ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.globe {
  padding: var(--pad-sec) 0;
  background: #0F172A;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.globe::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse at 30% 30%, rgba(5, 150, 105, 0.22) 0%, transparent 55%),
    radial-gradient(ellipse at 70% 70%, rgba(52, 211, 153, 0.14) 0%, transparent 55%);
  pointer-events: none;
}
.globe .mkt-container { position: relative; z-index: 1; }
.globe .h2 { color: #fff; }

.globe__wrap { position: relative; z-index: 1; }
.globe__svg {
  width: 100%;
  height: auto;
  max-height: 520px;
  display: block;
}
.globe__dots circle { fill: rgba(255, 255, 255, 0.55); }
.globe__arcs path { animation: dash-flow 4s linear infinite; }
@keyframes dash-flow {
  to { stroke-dashoffset: -16; }
}
.globe__labels text {
  fill: rgba(255, 255, 255, 0.7);
  font-family: var(--ff, 'Poppins', sans-serif);
  font-size: 12px;
  font-weight: 600;
}

.globe__stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
  margin-top: 32px;
  padding-top: 32px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}
.globe__stat strong {
  display: block;
  font-size: clamp(1.8rem, 3vw, 2.4rem);
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.02em;
  line-height: 1;
  margin-bottom: 8px;
}
.globe__stat span {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  9. FAQ                                                           ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.faq {
  padding: var(--pad-sec) 0;
  background: var(--bg);
}
.faq__grid {
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 64px;
  align-items: start;
}
.faq__head .h2 { margin-bottom: 16px; }
.faq__head p {
  font-size: 0.95rem;
  color: var(--muted);
  line-height: 1.7;
  margin: 12px 0 0;
}
.faq__head a {
  color: var(--em);
  font-weight: 600;
  text-decoration: none;
}
.faq__head a:hover { color: var(--em-2); text-decoration: underline; }

.faq__list { display: flex; flex-direction: column; gap: 12px; }
.faq__item {
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  background: #fff;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.faq__item--open {
  border-color: var(--em-ring);
  box-shadow: var(--s-sm);
}
.faq__q {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  background: transparent;
  border: none;
  padding: 19px 24px;
  font-size: 0.98rem;
  font-weight: 600;
  color: var(--ink);
  text-align: left;
  cursor: pointer;
  letter-spacing: -0.01em;
  font-family: inherit;
}
.faq__chev {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 3px;
  background: var(--bg-soft);
  color: var(--ink);
  flex-shrink: 0;
  transition: transform 0.25s, background 0.2s, color 0.2s;
}
.faq__item--open .faq__chev {
  transform: rotate(180deg);
  background: var(--em);
  color: #fff;
}
.faq__a {
  padding: 0 24px 22px;
  font-size: 0.93rem;
  line-height: 1.72;
  color: var(--slate);
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  10. FINAL CTA                                                    ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.cta-final {
  padding: var(--pad-sec) 0;
  background: #0F172A;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.cta-final::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 30%, rgba(5, 150, 105, 0.20) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(52, 211, 153, 0.10) 0%, transparent 50%);
}
.cta-final__inner {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 18px;
}
.cta-final p {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.65);
  margin: 0 0 14px;
  max-width: 520px;
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  RESPONSIVE                                                       ║
   ╚══════════════════════════════════════════════════════════════════╝ */
@media (max-width: 1024px) {
  .hero__inner { grid-template-columns: 1fr; gap: 56px; }
  .hero__right { min-height: auto; }
  .scards__grid { grid-template-columns: 1fr; }
  .igrid__grid { grid-template-columns: 1fr 1fr; }
  .counters__grid { grid-template-columns: 1fr 1fr; gap: 40px 24px; }
  .tabs__panel { grid-template-columns: 1fr; gap: 36px; }
  .faq__grid { grid-template-columns: 1fr; gap: 36px; }
}

@media (max-width: 768px) {
  .hero__metrics { gap: 18px; flex-wrap: wrap; }
  .igrid__grid { grid-template-columns: 1fr; }
  .tabs__nav { flex-wrap: wrap; }
  .globe__stats { grid-template-columns: 1fr; gap: 18px; }
  .teaser__grid { grid-template-columns: 1fr; }
}

@media (max-width: 580px) {
  .tab-btn { padding: 9px 14px; font-size: 0.8rem; }
  .counters__grid { grid-template-columns: 1fr; gap: 32px; }
  .row-cta { width: 100%; flex-direction: column; align-items: stretch; }
  .row-cta .btn { width: 100%; }
  .row-cta--center { flex-direction: row; }
}
</style>
