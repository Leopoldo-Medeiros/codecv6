<template>
  <section class="wl">
    <div class="mkt-container">
      <header class="wl__head">
        <span class="wl__kicker">On the roadmap</span>
        <h2 class="wl__title">What we build next is up to you</h2>
        <p class="wl__sub">
          Three tracks we're considering. Add your email to the one you want first —
          we build the one the most people vote for, and ping you the moment it opens.
        </p>
      </header>

      <div class="wl__grid">
        <div v-for="t in topics" :key="t.topic" class="wl__card">
          <span class="wl__soon">Coming soon</span>
          <h3 class="wl__cardtitle">{{ t.title }}</h3>
          <p class="wl__desc">{{ t.description }}</p>

          <form v-if="state[t.topic] !== 'done'" class="wl__form" @submit.prevent="join(t.topic)">
            <input
              v-model="emails[t.topic]"
              type="email"
              required
              placeholder="you@email.com"
              class="wl__input"
              :disabled="state[t.topic] === 'loading'"
              :aria-label="`Email for ${t.title}`"
            >
            <button type="submit" class="wl__btn" :disabled="state[t.topic] === 'loading'">
              {{ state[t.topic] === 'loading' ? 'Adding…' : 'Notify me' }}
            </button>
          </form>
          <p v-else class="wl__done">✓ You're on the list — we'll be in touch.</p>

          <p v-if="state[t.topic] === 'error'" class="wl__err">Something went wrong — please try again.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
// Demand-sensing: three candidate tracks. Slugs must match config/waitlist.php.
const topics = [
  {
    topic: 'observability',
    title: 'Observability Track',
    description: 'Operate what you build — traces, metrics, logs, and incident debugging with OpenTelemetry.',
  },
  {
    topic: 'ai-for-devs',
    title: 'AI for Developers',
    description: 'Ship faster with AI coding assistants and agentic workflows, the way real teams actually use them.',
  },
  {
    topic: 'ai-for-support',
    title: 'AI for IT Support & TSE',
    description: 'Troubleshoot faster with AI — built for support engineers, TSEs and IT pros, not just developers.',
  },
]

type Status = 'idle' | 'loading' | 'done' | 'error'
const emails = reactive<Record<string, string>>({})
const state = reactive<Record<string, Status>>({})
const apiBase = useRuntimeConfig().public.apiBase as string

async function join(topic: string) {
  const email = emails[topic]?.trim()
  if (!email) return
  state[topic] = 'loading'
  try {
    await $fetch('/api/public/waitlist', {
      baseURL: apiBase,
      method: 'POST',
      body: { email, topic, source: 'homepage' },
    })
    state[topic] = 'done'
  } catch {
    state[topic] = 'error'
  }
}
</script>

<style scoped>
.wl {
  padding: 88px 0;
  background: #f4f7f5;
  border-top: 1px solid #e3e9e5;
}
.wl__head {
  text-align: center;
  max-width: 640px;
  margin: 0 auto 48px;
}
.wl__kicker {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: #059669;
}
.wl__title {
  font-size: clamp(1.8rem, 3vw, 2.4rem);
  font-weight: 800;
  color: #111a22;
  margin: 12px 0 0;
  line-height: 1.15;
}
.wl__title::after {
  content: "";
  display: block;
  width: 54px;
  height: 4px;
  background: #059669;
  border-radius: 2px;
  margin: 18px auto 0;
}
.wl__sub {
  color: #4a5a52;
  font-size: 1.02rem;
  margin: 20px 0 0;
}
.wl__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 24px;
}
.wl__card {
  background: #fff;
  border: 1px solid #e3e9e5;
  border-radius: 6px;
  padding: 28px 24px;
  box-shadow: 0 1px 2px rgba(16, 34, 27, .05), 0 8px 24px rgba(16, 34, 27, .05);
  display: flex;
  flex-direction: column;
}
.wl__soon {
  align-self: flex-start;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #059669;
  background: rgba(5, 150, 105, .1);
  padding: 4px 10px;
  border-radius: 20px;
}
.wl__cardtitle {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111a22;
  margin: 16px 0 0;
}
.wl__desc {
  color: #4a5a52;
  font-size: .95rem;
  margin: 10px 0 20px;
  flex: 1;
}
.wl__form {
  display: flex;
  gap: 8px;
}
.wl__input {
  flex: 1;
  min-width: 0;
  padding: 11px 12px;
  border: 1px solid #cfd8d2;
  border-radius: 3px;
  font: inherit;
  font-size: .9rem;
  color: #111a22;
}
.wl__input:focus {
  outline: none;
  border-color: #059669;
  box-shadow: 0 0 0 3px rgba(5, 150, 105, .15);
}
.wl__btn {
  padding: 11px 16px;
  background: #059669;
  color: #fff;
  border: 0;
  border-radius: 3px;
  font-weight: 700;
  font-size: .8rem;
  letter-spacing: .04em;
  text-transform: uppercase;
  cursor: pointer;
  white-space: nowrap;
  transition: background .15s;
}
.wl__btn:hover:not(:disabled) { background: #047857; }
.wl__btn:disabled { opacity: .6; cursor: default; }
.wl__done {
  font-weight: 700;
  color: #047857;
  margin: 0;
}
.wl__err {
  color: #b4462b;
  font-size: .85rem;
  margin: 10px 0 0;
}
</style>
