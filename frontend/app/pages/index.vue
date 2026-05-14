<template>
  <NuxtLayout name="marketing">

    <!-- ─── 1. HERO (splite-style) ───────────────────── -->
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
              <g fill="rgba(15, 23, 42, 0.45)" font-family="Inter, sans-serif" font-size="11" font-weight="500" text-anchor="end">
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
              <g fill="rgba(15, 23, 42, 0.4)" font-family="Inter, sans-serif" font-size="10" font-weight="500" text-anchor="middle">
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


    <!-- ─── 2. LOGO CLOUD ────────────────────────────── -->
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


    <!-- ─── 3. FEATURE TABS (feature108-style) ───────── -->
    <section class="features">
      <div class="mkt-container">
        <header class="sec-head sec-head--center">
          <span class="eyebrow">OUR SERVICES</span>
          <h2 class="h2">Everything you need to<br>land your next role</h2>
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
              <span class="eyebrow">{{ activeTabData.badge }}</span>
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


    <!-- ─── 4. EVERVAULT-STYLE CARDS ─────────────────── -->
    <section class="vault">
      <div class="mkt-container">
        <header class="sec-head sec-head--center">
          <span class="eyebrow">CAPABILITIES</span>
          <h2 class="h2">Built for serious career growth</h2>
        </header>

        <div class="vault__grid">
          <div
            v-for="(c, i) in vaultCards"
            :key="c.title"
            class="evercard"
            @mousemove="onVaultMove($event, i)"
            @mouseleave="onVaultLeave(i)"
            :style="{ '--x': (vaultPos[i]?.x ?? 0) + 'px', '--y': (vaultPos[i]?.y ?? 0) + 'px', '--lit': vaultPos[i]?.lit ?? 0 }"
          >
            <div class="evercard__pattern" aria-hidden="true"></div>
            <div class="evercard__cross evercard__cross--tl" aria-hidden="true"></div>
            <div class="evercard__cross evercard__cross--tr" aria-hidden="true"></div>
            <div class="evercard__cross evercard__cross--bl" aria-hidden="true"></div>
            <div class="evercard__cross evercard__cross--br" aria-hidden="true"></div>
            <div class="evercard__content">
              <div class="evercard__icon">
                <component :is="c.icon" />
              </div>
              <h3>{{ c.title }}</h3>
              <p>{{ c.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ─── 5. WORLD MAP ─────────────────────────────── -->
    <section class="globe">
      <div class="mkt-container">
        <header class="sec-head sec-head--center">
          <span class="eyebrow eyebrow--light">GLOBAL REACH</span>
          <h2 class="h2 h2--light">Connecting Irish IT talent<br>to teams worldwide</h2>
          <p class="sec-sub sec-sub--light">
            From Dublin to Berlin, London, New York, Tokyo, and Sydney —
            our placements span the globe's leading tech hubs.
          </p>
        </header>

        <div class="globe__wrap">
          <svg viewBox="0 0 1000 500" class="globe__svg" aria-hidden="true">
            <!-- Stylised dotted world map -->
            <g class="globe__dots">
              <circle v-for="(d, i) in mapDots" :key="i" :cx="d[0]" :cy="d[1]" r="2.5" />
            </g>

            <!-- Connection arcs from Dublin to other hubs -->
            <g class="globe__arcs">
              <path
                v-for="(arc, i) in arcs"
                :key="i"
                :d="arc"
                fill="none"
                stroke="#34D399"
                stroke-width="1.2"
                stroke-dasharray="4 4"
                stroke-linecap="round"
              />
            </g>

            <!-- City markers -->
            <g class="globe__markers">
              <g v-for="city in cities" :key="city.name" :transform="`translate(${city.x},${city.y})`">
                <circle r="14" fill="rgba(52, 211, 153, 0.15)" />
                <circle r="6" fill="rgba(52, 211, 153, 0.45)" />
                <circle r="3" fill="#34D399" />
              </g>
            </g>

            <!-- City labels -->
            <g class="globe__labels">
              <text
                v-for="city in cities"
                :key="city.name + '-l'"
                :x="city.x + 14"
                :y="city.y + 4"
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


    <!-- ─── 6. TESTIMONIALS COLUMNS ──────────────────── -->
    <section class="testi">
      <div class="mkt-container">
        <header class="sec-head sec-head--center">
          <span class="eyebrow">TESTIMONIALS</span>
          <h2 class="h2">Voices from our community</h2>
          <p class="sec-sub">Real outcomes from professionals who've been through the programme.</p>
        </header>

        <div class="testi__cols">
          <div
            v-for="(col, ci) in testiCols"
            :key="ci"
            class="testi__col"
            :class="`testi__col--${ci}`"
          >
            <div class="testi__col-inner">
              <article v-for="(t, ti) in [...col, ...col]" :key="ci + '-' + ti" class="tcard">
                <div class="tcard__stars">★★★★★</div>
                <p class="tcard__text">"{{ t.text }}"</p>
                <footer class="tcard__foot">
                  <img :src="t.img" :alt="t.author" />
                  <div>
                    <strong>{{ t.author }}</strong>
                    <span>{{ t.role }}</span>
                  </div>
                </footer>
              </article>
            </div>
          </div>
        </div>

        <div class="testi__fade testi__fade--top" aria-hidden="true"></div>
        <div class="testi__fade testi__fade--bot" aria-hidden="true"></div>
      </div>
    </section>


    <!-- ─── 7. FAQ ───────────────────────────────────── -->
    <section class="faq">
      <div class="mkt-container faq__grid">
        <div class="faq__head">
          <span class="eyebrow">FAQ</span>
          <h2 class="h2">Got questions?<br>We've got answers.</h2>
          <p>
            Can't find what you're looking for?
            <NuxtLink to="/contact">Reach out directly →</NuxtLink>
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


    <!-- ─── 8. FINAL CTA ─────────────────────────────── -->
    <section class="cta-final">
      <div class="mkt-container cta-final__inner">
        <span class="eyebrow eyebrow--light">READY TO START?</span>
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

<script setup lang="ts">
import { h, reactive } from 'vue'

definePageMeta({ layout: false })

useHead({
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: 'anonymous' },
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap',
    },
  ],
})

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

/* === Tab icons === */
const stroke = '#0F172A'
const IconDoc = () => h('svg', { viewBox: '0 0 24 24', width: 18, height: 18, fill: 'none' }, [
  h('path', { d: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z', stroke, 'stroke-width': 1.8, 'stroke-linejoin': 'round' }),
  h('path', { d: 'M14 2v6h6M8 13h8M8 17h5', stroke, 'stroke-width': 1.8, 'stroke-linecap': 'round' }),
])
const IconChat = () => h('svg', { viewBox: '0 0 24 24', width: 18, height: 18, fill: 'none' }, [
  h('path', { d: 'M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z', stroke, 'stroke-width': 1.8, 'stroke-linejoin': 'round' }),
])
const IconRoad = () => h('svg', { viewBox: '0 0 24 24', width: 18, height: 18, fill: 'none' }, [
  h('path', { d: 'M3 6h10M3 12h14M3 18h7', stroke, 'stroke-width': 1.8, 'stroke-linecap': 'round' }),
  h('circle', { cx: 19, cy: 6, r: 2, stroke, 'stroke-width': 1.8 }),
])
const IconBriefcase = () => h('svg', { viewBox: '0 0 24 24', width: 18, height: 18, fill: 'none' }, [
  h('rect', { x: 3, y: 7, width: 18, height: 13, rx: 2, stroke, 'stroke-width': 1.8 }),
  h('path', { d: 'M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18', stroke, 'stroke-width': 1.8, 'stroke-linecap': 'round' }),
])

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

/* === Evervault cards === */
const IconBolt = () => h('svg', { viewBox: '0 0 24 24', width: 22, height: 22, fill: 'none' }, [
  h('path', { d: 'M13 2L3 14h7l-1 8 10-12h-7l1-8z', stroke: '#059669', 'stroke-width': 1.8, 'stroke-linejoin': 'round' }),
])
const IconShield = () => h('svg', { viewBox: '0 0 24 24', width: 22, height: 22, fill: 'none' }, [
  h('path', { d: 'M12 2l8 4v6c0 5-3.5 9.4-8 10-4.5-.6-8-5-8-10V6l8-4z', stroke: '#059669', 'stroke-width': 1.8, 'stroke-linejoin': 'round' }),
  h('path', { d: 'M9 12l2 2 4-4', stroke: '#059669', 'stroke-width': 1.8, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }),
])
const IconCompass = () => h('svg', { viewBox: '0 0 24 24', width: 22, height: 22, fill: 'none' }, [
  h('circle', { cx: 12, cy: 12, r: 9, stroke: '#059669', 'stroke-width': 1.8 }),
  h('path', { d: 'M16 8l-2 6-6 2 2-6 6-2z', stroke: '#059669', 'stroke-width': 1.8, 'stroke-linejoin': 'round' }),
])

const vaultCards = [
  { icon: IconBolt,    title: 'AI-powered CV review', desc: 'Instant ATS scoring and keyword analysis tuned for the Irish tech market.' },
  { icon: IconShield,  title: 'Verified placements',  desc: 'Every coach we work with has placed engineers at Dublin firms in the last 12 months.' },
  { icon: IconCompass, title: 'Career direction',     desc: 'Personalised guidance from where you are now to where you want to be in 12 months.' },
]
const vaultPos = reactive(vaultCards.map(() => ({ x: 0, y: 0, lit: 0 })))
function onVaultMove(e: MouseEvent, i: number) {
  const pos = vaultPos[i]
  if (!pos) return
  const rect = (e.currentTarget as HTMLElement).getBoundingClientRect()
  pos.x = e.clientX - rect.left
  pos.y = e.clientY - rect.top
  pos.lit = 1
}
function onVaultLeave(i: number) {
  const pos = vaultPos[i]
  if (pos) pos.lit = 0
}

/* === World map === */
const cities = [
  { name: 'Dublin',   x: 470, y: 165 },
  { name: 'London',   x: 490, y: 180 },
  { name: 'Berlin',   x: 545, y: 175 },
  { name: 'New York', x: 245, y: 200 },
  { name: 'Tokyo',    x: 845, y: 215 },
  { name: 'Sydney',   x: 875, y: 380 },
]
// Arcs Dublin → each other city
const arcs = computed(() =>
  cities.slice(1).map(c => {
    const x1 = 470, y1 = 165, x2 = c.x, y2 = c.y
    const cx = (x1 + x2) / 2
    const cy = Math.min(y1, y2) - Math.abs(x2 - x1) * 0.25
    return `M${x1} ${y1} Q ${cx} ${cy} ${x2} ${y2}`
  })
)
// Stylised dotted world map (clusters approximating continents).
// Generated with a seeded PRNG so the dot positions are identical on the
// server and the client — otherwise Vue would log a hydration mismatch
// and the map would jump on hydrate.
const mapDots = (() => {
  let seed = 0x9E3779B1
  const rand = () => {
    seed = (seed + 0x6D2B79F5) | 0
    let t = seed
    t = Math.imul(t ^ (t >>> 15), t | 1)
    t ^= t + Math.imul(t ^ (t >>> 7), t | 61)
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296
  }
  const dots: number[][] = []
  const blobs: Array<[number, number, number, number, number]> = [
    // [cx, cy, w, h, density]
    [120, 150, 200, 90, 12],  // North America
    [255, 320,  60, 130, 7],  // South America
    [490, 170,  80,  50, 7],  // Europe
    [540, 290,  80, 140, 9],  // Africa
    [720, 200, 220,  90, 14], // Asia
    [860, 370,  70,  50, 6],  // Australia
  ]
  for (const [cx, cy, w, h2, n] of blobs) {
    for (let i = 0; i < n; i++) {
      for (let j = 0; j < (n - 2); j++) {
        const x = cx + (rand() - 0.5) * w
        const y = cy + (rand() - 0.5) * h2
        const dx = (x - cx) / (w / 2)
        const dy = (y - cy) / (h2 / 2)
        if (dx * dx + dy * dy <= 1.05) dots.push([Math.round(x), Math.round(y)])
      }
    }
  }
  return dots
})()

const globeStats = [
  { val: '12+', label: 'Countries served' },
  { val: '200+', label: 'Placements globally' },
  { val: '24/7', label: 'Consultant support' },
]

/* === Testimonials (3 columns) === */
const testiCols = [
  [
    { text: 'CODECV helped me land a senior developer role in Dublin within 3 months. The CV rewrite was a complete game-changer.', author: 'Tomasz K.', role: 'Senior Java Developer', img: '/images/review-author-1.jpg' },
    { text: 'The structured learning path gave me a clear roadmap. I went from junior to mid-level engineer in 6 months.', author: 'Maria S.', role: 'Software Engineer', img: '/images/review-author-2.jpg' },
    { text: 'My consultant knew exactly which Dublin firms were hiring and helped me tailor every application.', author: 'Arun P.', role: 'Cloud Engineer', img: '/images/review-author-3.jpg' },
  ],
  [
    { text: 'I was stuck for years. CODECV got me into a tech lead role in four months — start to finish.', author: 'Ciara M.', role: 'Tech Lead', img: '/images/review-author-4.jpg' },
    { text: 'Mock interviews with senior engineers were brutal but exactly what I needed before going for FAANG.', author: 'Daniel R.', role: 'Software Engineer · Google', img: '/images/review-author-1.jpg' },
    { text: 'The platform tracks every step. I always knew exactly what to do next, no more drifting.', author: 'Sofia L.', role: 'Frontend Engineer', img: '/images/review-author-2.jpg' },
  ],
  [
    { text: 'Worth every euro. My CV went from getting rejected to getting 7 callbacks in the first week.', author: 'Patrick D.', role: 'Backend Engineer', img: '/images/review-author-3.jpg' },
    { text: 'Coaches who actually work in the Irish market. Their advice was specific, not generic LinkedIn fluff.', author: 'Aoife N.', role: 'Data Engineer', img: '/images/review-author-4.jpg' },
    { text: 'The salary negotiation playbook alone paid for the entire programme three times over.', author: 'Liam O.', role: 'Senior DevOps Engineer', img: '/images/review-author-1.jpg' },
  ],
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
</script>

<style scoped>
/* ╔══════════════════════════════════════════════════════════════════╗
   ║  DESIGN TOKENS — defined on every section root so they cascade   ║
   ║  to children. (Vue scoped CSS rewrites :root so we can't use it.) ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.hero, .logos, .features, .vault, .globe, .testi, .faq, .cta-final {
  /* Color */
  --em:       #059669;
  --em-2:     #047857;
  --em-3:     #064E3B;
  --em-soft:  #D1FAE5;
  --em-ring:  #A7F3D0;
  --em-ring-2:#6EE7B7;
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
  /* Radius */
  --r-sm:     10px;
  --r-md:     16px;
  --r-lg:     24px;
  --r-xl:     32px;
  /* Shadow */
  --s-sm:     0 2px 8px rgba(15, 23, 42, 0.06);
  --s-md:     0 8px 24px rgba(15, 23, 42, 0.08);
  --s-lg:     0 20px 50px rgba(15, 23, 42, 0.14);

  font-family: 'Inter', sans-serif;
  color: #0F172A;
}

/* ── TYPE SYSTEM (cohesive across the page) ────────────────────────── */
.h1, .h2, .h3 {
  font-family: 'Manrope', 'Inter', sans-serif;
  font-weight: 800;
  letter-spacing: -0.025em;
  color: var(--ink);
  margin: 0;
}
.h1 {
  font-size: clamp(2.4rem, 4.4vw, 3.9rem);
  line-height: 1.05;
}
.h1__em { color: var(--em); }
.h2 {
  font-size: clamp(1.9rem, 3.4vw, 2.6rem);
  line-height: 1.12;
}
.h2--light { color: #fff; }
.h3 {
  font-size: clamp(1.3rem, 2vw, 1.6rem);
  font-weight: 700;
  line-height: 1.2;
}
.lead {
  font-size: 1.02rem;
  line-height: 1.72;
  color: var(--slate);
  margin: 0;
  max-width: 460px;
}
.eyebrow {
  display: inline-block;
  font-family: 'Inter', sans-serif;
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  color: var(--em);
  text-transform: uppercase;
  margin-bottom: 14px;
}
.eyebrow--light { color: var(--em-ring); }
.sec-sub {
  font-size: 1rem;
  line-height: 1.7;
  color: var(--muted);
  max-width: 560px;
  margin: 16px auto 0;
}
.sec-sub--light { color: rgba(255, 255, 255, 0.65); }

.sec-head { margin-bottom: 48px; }
.sec-head--center { text-align: center; }
.sec-head--center .h2 { margin-left: auto; margin-right: auto; }

/* ── BUTTONS ───────────────────────────────────────────────────────── */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 24px;
  font-family: 'Inter', sans-serif;
  font-size: 0.92rem;
  font-weight: 600;
  border-radius: 100px;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, transform 0.18s, box-shadow 0.2s, border-color 0.2s;
  white-space: nowrap;
}
.btn--em { background: var(--em); color: #fff; }
.btn--em:hover { background: var(--em-2); transform: translateY(-2px); box-shadow: 0 12px 26px rgba(5, 150, 105, 0.32); }
.btn--ghost {
  background: transparent;
  color: var(--ink);
  border: 1.5px solid var(--border-2);
}
.btn--ghost:hover { border-color: var(--ink); background: var(--bg-soft); }
.btn--ghost-light {
  background: transparent;
  color: rgba(255, 255, 255, 0.85);
  border: 1.5px solid rgba(255, 255, 255, 0.25);
}
.btn--ghost-light:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.55); }
.btn--em-soft {
  background: var(--em-soft);
  color: var(--em-2);
}
.btn--em-soft:hover { background: var(--em-ring); }
.btn--lg { padding: 16px 32px; font-size: 0.98rem; }

.row-cta {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
  margin-bottom: 44px;
}
.row-cta--center { justify-content: center; margin-bottom: 0; }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  1. HERO                                                         ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.hero {
  position: relative;
  padding: clamp(80px, 8vw, 120px) 0 var(--pad-sec);
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
  border-radius: 100px;
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
  font-family: 'Manrope', sans-serif;
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

/* Hero right column — dark composition card (New Relic style) */
.hero__right {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 700px;
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
   ║  2. LOGO CLOUD                                                   ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.logos {
  padding: 56px 0;
  background: var(--bg);
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
  font-family: 'Manrope', sans-serif;
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--muted-2);
  opacity: 0.75;
  transition: opacity 0.2s, color 0.2s;
}
.logos__item:hover { opacity: 1; color: var(--ink); }

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  3. FEATURE TABS                                                 ║
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
  margin-bottom: 48px;
  padding: 6px;
  background: var(--bg-soft);
  border: 1px solid var(--border);
  border-radius: 100px;
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
}
.tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  border: none;
  font-family: 'Inter', sans-serif;
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--slate);
  padding: 10px 20px;
  border-radius: 100px;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.tab-btn:hover { color: var(--ink); }
.tab-btn--active {
  background: #fff;
  color: var(--ink);
  box-shadow: var(--s-sm);
}
.tab-btn :deep(svg path) { transition: stroke 0.2s; }

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
  font-size: 1rem;
  line-height: 1.72;
  color: var(--slate);
  margin: 16px 0 24px;
}
.tabs__panel-text .eyebrow { margin-bottom: 12px; }
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
   ║  4. EVERVAULT CARDS                                              ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.vault {
  padding: var(--pad-sec) 0;
  background: var(--bg-soft);
}
.vault__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.evercard {
  position: relative;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  padding: 32px 28px;
  min-height: 260px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
}
.evercard:hover { transform: translateY(-3px); box-shadow: var(--s-md); border-color: var(--em-ring); }

/* Animated grid pattern revealed by cursor (evervault effect) */
.evercard__pattern {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(0deg, transparent 23%, rgba(5,150,105,0.32) 24%, rgba(5,150,105,0.32) 26%, transparent 27%),
    linear-gradient(90deg, transparent 23%, rgba(5,150,105,0.32) 24%, rgba(5,150,105,0.32) 26%, transparent 27%);
  background-size: 24px 24px;
  opacity: var(--lit, 0);
  -webkit-mask-image: radial-gradient(circle 160px at var(--x, -100px) var(--y, -100px), #000 0%, rgba(0,0,0,0.4) 30%, transparent 75%);
          mask-image: radial-gradient(circle 160px at var(--x, -100px) var(--y, -100px), #000 0%, rgba(0,0,0,0.4) 30%, transparent 75%);
  transition: opacity 0.18s ease;
  pointer-events: none;
}
.evercard__cross {
  position: absolute;
  width: 8px;
  height: 8px;
  pointer-events: none;
}
.evercard__cross::before,
.evercard__cross::after {
  content: '';
  position: absolute;
  background: var(--em);
}
.evercard__cross::before { width: 100%; height: 1px; top: 50%; left: 0; }
.evercard__cross::after  { width: 1px; height: 100%; top: 0; left: 50%; }
.evercard__cross--tl { top: -4px; left: -4px; }
.evercard__cross--tr { top: -4px; right: -4px; }
.evercard__cross--bl { bottom: -4px; left: -4px; }
.evercard__cross--br { bottom: -4px; right: -4px; }

.evercard__content { position: relative; z-index: 1; }
.evercard__icon {
  width: 48px;
  height: 48px;
  background: var(--em-soft);
  border-radius: var(--r-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}
.evercard h3 {
  font-family: 'Manrope', sans-serif;
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--ink);
  margin: 0 0 10px;
  letter-spacing: -0.01em;
}
.evercard p {
  font-size: 0.92rem;
  line-height: 1.72;
  color: var(--muted);
  margin: 0;
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  5. WORLD MAP                                                    ║
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
.globe .h2,
.globe .h2--light { color: #fff; }
.globe .eyebrow { color: #A7F3D0; }
.globe .sec-sub { color: rgba(255, 255, 255, 0.65); }

.globe__wrap {
  position: relative;
  z-index: 1;
}
.globe__svg {
  width: 100%;
  height: auto;
  max-height: 520px;
  display: block;
}
.globe__dots circle { fill: rgba(255, 255, 255, 0.55); }
.globe__arcs path {
  animation: dash-flow 4s linear infinite;
}
@keyframes dash-flow {
  to { stroke-dashoffset: -16; }
}
.globe__labels text {
  fill: rgba(255, 255, 255, 0.7);
  font-family: 'Inter', sans-serif;
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
  font-family: 'Manrope', sans-serif;
  font-size: clamp(1.8rem, 3vw, 2.4rem);
  font-weight: 800;
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
   ║  6. TESTIMONIALS COLUMNS                                         ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.testi {
  padding: var(--pad-sec) 0;
  background: var(--bg);
  position: relative;
  overflow: hidden;
}
.testi__cols {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  max-height: 560px;
  overflow: hidden;
  mask-image: linear-gradient(to bottom, transparent 0%, #000 12%, #000 88%, transparent 100%);
  -webkit-mask-image: linear-gradient(to bottom, transparent 0%, #000 12%, #000 88%, transparent 100%);
}
.testi__col {
  display: flex;
  flex-direction: column;
}
.testi__col-inner {
  display: flex;
  flex-direction: column;
  gap: 20px;
  animation: scroll-up 38s linear infinite;
}
.testi__col--1 .testi__col-inner { animation: scroll-down 44s linear infinite; }
.testi__col--2 .testi__col-inner { animation: scroll-up 50s linear infinite; }
@keyframes scroll-up {
  from { transform: translateY(0); }
  to   { transform: translateY(-50%); }
}
@keyframes scroll-down {
  from { transform: translateY(-50%); }
  to   { transform: translateY(0); }
}

.tcard {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  padding: 24px;
  box-shadow: var(--s-sm);
}
.tcard__stars {
  color: var(--em);
  font-size: 0.9rem;
  letter-spacing: 0.1em;
  margin-bottom: 14px;
}
.tcard__text {
  font-family: 'Manrope', sans-serif;
  font-size: 0.92rem;
  line-height: 1.65;
  color: var(--ink-2);
  margin: 0 0 18px;
  font-weight: 500;
}
.tcard__foot {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0;
}
.tcard__foot img {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  object-fit: cover;
  background: var(--border);
}
.tcard__foot strong {
  display: block;
  font-family: 'Manrope', sans-serif;
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--ink);
}
.tcard__foot span {
  display: block;
  font-size: 0.74rem;
  color: var(--muted);
  font-weight: 500;
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  7. FAQ                                                          ║
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
  margin: 0;
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
  padding: 20px 24px;
  font-family: 'Manrope', sans-serif;
  font-size: 1rem;
  font-weight: 600;
  color: var(--ink);
  text-align: left;
  cursor: pointer;
  letter-spacing: -0.01em;
}
.faq__chev {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 50%;
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
  font-size: 0.94rem;
  line-height: 1.72;
  color: var(--slate);
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  8. FINAL CTA                                                    ║
   ╚══════════════════════════════════════════════════════════════════╝ */
.cta-final {
  padding: var(--pad-sec) 0;
  background: #0F172A;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.cta-final .h2,
.cta-final .h2--light { color: #fff; }
.cta-final .eyebrow { color: #A7F3D0; }
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
  gap: 20px;
}
.cta-final__inner .h2 { margin: 8px 0 8px; }
.cta-final p {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.65);
  margin: 0 0 16px;
  max-width: 520px;
}

/* ╔══════════════════════════════════════════════════════════════════╗
   ║  RESPONSIVE                                                      ║
   ╚══════════════════════════════════════════════════════════════════╝ */
@media (max-width: 1024px) {
  .hero__inner { grid-template-columns: 1fr; gap: 56px; }
  .hero__right { min-height: auto; }

  .tabs__panel { grid-template-columns: 1fr; gap: 36px; }
  .vault__grid { grid-template-columns: 1fr 1fr; }
  .testi__cols { grid-template-columns: 1fr 1fr; max-height: 480px; }
  .faq__grid { grid-template-columns: 1fr; gap: 36px; }
}

@media (max-width: 768px) {
  .hero__metrics { gap: 18px; flex-wrap: wrap; }

  .tabs__nav { flex-wrap: wrap; }
  .vault__grid { grid-template-columns: 1fr; }
  .testi__cols { grid-template-columns: 1fr; max-height: 600px; }
  .testi__col--2 { display: none; }
  .globe__stats { grid-template-columns: 1fr; gap: 18px; }
}

@media (max-width: 580px) {
  .tabs__nav { padding: 4px; }
  .tab-btn { padding: 8px 14px; font-size: 0.8rem; }
  .row-cta { width: 100%; flex-direction: column; align-items: stretch; }
  .row-cta .btn { width: 100%; }
  .row-cta--center { flex-direction: row; }
}
</style>
