<template>
  <NuxtLayout name="marketing">
    <div class="skillp">
      <div class="mkt-container">

        <!-- Loading -->
        <div v-if="pending" class="skillp__state">
          <p>Loading profile…</p>
        </div>

        <!-- Not found / private -->
        <div v-else-if="!profile" class="skillp__state">
          <h1 class="skillp__missing-title">Profile not found</h1>
          <p class="skillp__missing-sub">
            This skill profile doesn't exist or its owner has set it to private.
          </p>
          <NuxtLink to="/" class="mkt-btn mkt-btn--primary">Back to home</NuxtLink>
        </div>

        <template v-else>
          <!-- Header -->
          <header class="skillp__head">
            <div>
              <p class="skillp__verified">✓ Verified by CODECV</p>
              <h1 class="skillp__name">{{ profile.fullname }}</h1>
              <p class="skillp__meta">
                <span v-if="profile.profession">{{ profile.profession }}</span>
                <span v-if="profile.level" class="skillp__level">{{ profile.level }}</span>
                <span v-if="profile.member_since" class="skillp__since">practicing since {{ formatDate(profile.member_since) }}</span>
              </p>
              <p v-if="profile.goal" class="skillp__goal">“{{ profile.goal }}”</p>
            </div>

            <div v-if="hasLinks" class="skillp__links">
              <a v-if="profile.links.github" :href="profile.links.github" target="_blank" rel="noopener">GitHub ↗</a>
              <a v-if="profile.links.linkedin" :href="profile.links.linkedin" target="_blank" rel="noopener">LinkedIn ↗</a>
              <a v-if="profile.links.website" :href="profile.links.website" target="_blank" rel="noopener">Website ↗</a>
            </div>
          </header>

          <!-- Stats -->
          <div class="skillp__stats">
            <div class="skillp__stat">
              <strong>{{ profile.stats.xp_points }}</strong>
              <span>XP earned</span>
            </div>
            <div class="skillp__stat">
              <strong>🔥 {{ profile.stats.current_streak }}</strong>
              <span>day streak</span>
            </div>
            <div class="skillp__stat">
              <strong>{{ profile.stats.longest_streak }}</strong>
              <span>longest streak</span>
            </div>
            <div class="skillp__stat">
              <strong>{{ profile.completed_challenges.length }}</strong>
              <span>challenges solved</span>
            </div>
          </div>

          <!-- Certifications — the employer-facing seal -->
          <section v-if="certifications.length" class="skillp__certs">
            <div v-for="cert in certifications" :key="cert.key" class="skillp__cert">
              <div class="skillp__cert-icon">{{ cert.icon }}</div>
              <div class="skillp__cert-body">
                <p class="skillp__cert-kicker">✓ Certified by CODECV</p>
                <strong class="skillp__cert-name">{{ cert.name }}</strong>
                <p class="skillp__cert-desc">{{ cert.description }}</p>
              </div>
            </div>
          </section>

          <div class="skillp__cols">
            <!-- Left: challenges -->
            <section>
              <h2 class="skillp__h2">Completed challenges</h2>
              <ul v-if="profile.completed_challenges.length" class="skillp__challenges">
                <li v-for="challenge in profile.completed_challenges" :key="challenge.title + challenge.completed_at">
                  <span class="skillp__challenge-title">{{ challenge.title }}</span>
                  <span class="skillp__difficulty" :data-level="challenge.difficulty">{{ challenge.difficulty }}</span>
                  <span class="skillp__date">{{ formatDate(challenge.completed_at) }}</span>
                </li>
              </ul>
              <p v-else class="skillp__empty">No challenges completed yet.</p>
            </section>

            <!-- Right: stack + badges -->
            <aside>
              <section v-if="profile.stack.length">
                <h2 class="skillp__h2">Stack</h2>
                <div class="skillp__chips">
                  <span v-for="tech in profile.stack" :key="tech" class="skillp__chip">{{ tech }}</span>
                </div>
              </section>

              <section v-if="achievements.length" class="skillp__badges-section">
                <h2 class="skillp__h2">Badges</h2>
                <ul class="skillp__badges">
                  <li v-for="badge in achievements" :key="badge.key">
                    <span class="skillp__badge-icon">{{ badge.icon }}</span>
                    <div>
                      <strong>{{ badge.name }}</strong>
                      <p>{{ badge.description }}</p>
                    </div>
                  </li>
                </ul>
              </section>
            </aside>
          </div>

          <!-- CTA -->
          <div class="skillp__cta">
            <p>Practice real-world challenges and build your own verified skill profile.</p>
            <NuxtLink to="/register" class="mkt-btn mkt-btn--primary mkt-btn--lg">Start practicing free</NuxtLink>
          </div>
        </template>

      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

interface PublicSkillProfile {
  fullname: string
  profession: string | null
  level: string | null
  stack: string[]
  goal: string | null
  links: { github?: string, linkedin?: string, website?: string }
  member_since: string | null
  stats: { xp_points: number, current_streak: number, longest_streak: number }
  badges: { key: string, category: string, name: string, description: string, icon: string, earned_at: string }[]
  completed_challenges: { title: string, difficulty: string, completed_at: string }[]
}

const route = useRoute()
const config = useRuntimeConfig()

const profile = ref<PublicSkillProfile | null>(null)
const pending = ref(true)

const hasLinks = computed(() =>
  Boolean(profile.value && Object.keys(profile.value.links).length),
)

// Certifications are shown as a prominent seal; other badges stay as chips.
const certifications = computed(() =>
  profile.value?.badges.filter(b => b.category === 'certification') ?? [],
)
const achievements = computed(() =>
  profile.value?.badges.filter(b => b.category !== 'certification') ?? [],
)

useSeoMeta({
  title: () => profile.value ? `${profile.value.fullname} — Skill Profile` : 'Skill Profile',
  description: () => profile.value
    ? `${profile.value.fullname}'s verified practice history on CODECV: ${profile.value.stats.xp_points} XP, ${profile.value.completed_challenges.length} challenges completed.`
    : 'Verified developer skill profile on CODECV.',
  robots: 'index, follow',
})

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('en-IE', { month: 'short', year: 'numeric' })
}

onMounted(async () => {
  try {
    const response = await $fetch<{ data: PublicSkillProfile }>(
      `/api/public/profile/${route.params.slug}`,
      { baseURL: config.public.apiBase as string },
    )
    profile.value = response.data
  } catch {
    profile.value = null
  } finally {
    pending.value = false
  }
})
</script>

<style scoped>
.skillp {
  padding: clamp(48px, 7vw, 88px) 0 clamp(64px, 8vw, 104px);
  font-family: var(--ff, 'Poppins', sans-serif);
  color: var(--text, #17212B);
  min-height: 60vh;
}

/* ── States ─────────────────────────────────────────── */
.skillp__state {
  text-align: center;
  padding: 80px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  color: var(--muted, #8B95A1);
}
.skillp__missing-title { font-size: 28px; font-weight: 700; color: var(--text, #17212B); margin: 0; }
.skillp__missing-sub { max-width: 44ch; }

/* ── Header ─────────────────────────────────────────── */
.skillp__head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
  margin-bottom: 32px;
}
.skillp__verified {
  display: inline-block;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--accent, #059669);
  margin: 0 0 8px;
}
.skillp__name {
  font-size: clamp(28px, 4vw, 40px);
  font-weight: 700;
  line-height: 1.15;
  margin: 0 0 10px;
}
.skillp__meta {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin: 0;
  color: var(--muted, #8B95A1);
  font-size: 15px;
}
.skillp__level {
  padding: 2px 10px;
  border-radius: 3px;
  background: var(--accent-light, rgba(5, 150, 105, 0.08));
  color: var(--accent, #059669);
  font-size: 12.5px;
  font-weight: 600;
  text-transform: capitalize;
}
.skillp__since { font-size: 13.5px; }
.skillp__goal {
  margin: 14px 0 0;
  font-style: italic;
  color: var(--text-body, #45505C);
  max-width: 56ch;
}
.skillp__links { display: flex; gap: 14px; flex-wrap: wrap; }
.skillp__links a {
  color: var(--accent, #059669);
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
}
.skillp__links a:hover { text-decoration: underline; }

/* ── Stats ──────────────────────────────────────────── */
.skillp__stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 40px;
}
.skillp__stat {
  background: #fff;
  border: 1px solid var(--border, #E9EDF2);
  border-radius: 6px;
  padding: 18px;
  text-align: center;
  box-shadow: var(--shadow-sm, 0 2px 10px rgba(23, 33, 43, 0.06));
}
.skillp__stat strong {
  display: block;
  font-size: 26px;
  font-weight: 700;
  line-height: 1.1;
}
.skillp__stat span { font-size: 12.5px; color: var(--muted, #8B95A1); }

/* ── Certifications (the seal) ──────────────────────── */
.skillp__certs {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 16px;
  margin-bottom: 40px;
}
.skillp__cert {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 20px 22px;
  border: 1.5px solid var(--accent, #059669);
  border-radius: 8px;
  background: var(--accent-light, rgba(5, 150, 105, 0.08));
  box-shadow: var(--shadow-sm, 0 2px 10px rgba(23, 33, 43, 0.06));
}
.skillp__cert-icon {
  flex-shrink: 0;
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 30px;
  background: #fff;
  border: 1.5px solid var(--accent, #059669);
  border-radius: 50%;
}
.skillp__cert-kicker {
  margin: 0 0 4px;
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--accent, #059669);
}
.skillp__cert-name { font-size: 18px; font-weight: 700; line-height: 1.2; display: block; }
.skillp__cert-desc { margin: 6px 0 0; font-size: 13.5px; color: var(--text-body, #45505C); max-width: 46ch; }

/* ── Columns ────────────────────────────────────────── */
.skillp__cols {
  display: grid;
  grid-template-columns: 1.5fr 1fr;
  gap: 40px;
  align-items: start;
}
.skillp__h2 {
  font-size: 17px;
  font-weight: 700;
  margin: 0 0 14px;
  padding-bottom: 10px;
  position: relative;
}
.skillp__h2::after {
  content: '';
  position: absolute;
  left: 0; bottom: 0;
  width: 36px; height: 3px;
  background: var(--accent, #059669);
}

.skillp__challenges { list-style: none; margin: 0; padding: 0; }
.skillp__challenges li {
  display: flex;
  align-items: baseline;
  gap: 12px;
  padding: 11px 0;
  border-bottom: 1px solid var(--border, #E9EDF2);
  font-size: 14.5px;
}
.skillp__challenge-title { flex: 1; font-weight: 500; }
.skillp__difficulty {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 2px 8px;
  border-radius: 3px;
  background: var(--accent-light, rgba(5, 150, 105, 0.08));
  color: var(--accent, #059669);
}
.skillp__difficulty[data-level="advanced"],
.skillp__difficulty[data-level="expert"] {
  background: rgba(168, 113, 10, 0.1);
  color: #A8710A;
}
.skillp__date { font-size: 12.5px; color: var(--muted, #8B95A1); white-space: nowrap; }
.skillp__empty { color: var(--muted, #8B95A1); font-size: 14.5px; }

.skillp__chips { display: flex; flex-wrap: wrap; gap: 8px; }
.skillp__chip {
  padding: 4px 12px;
  border: 1px solid var(--border, #E9EDF2);
  border-radius: 3px;
  font-size: 13px;
  font-weight: 500;
  background: #fff;
}
.skillp__badges-section { margin-top: 32px; }
.skillp__badges { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 12px; }
.skillp__badges li { display: flex; gap: 12px; align-items: flex-start; }
.skillp__badge-icon { font-size: 22px; line-height: 1.2; }
.skillp__badges strong { font-size: 14px; display: block; }
.skillp__badges p { margin: 2px 0 0; font-size: 13px; color: var(--muted, #8B95A1); }

/* ── CTA ────────────────────────────────────────────── */
.skillp__cta {
  margin-top: 56px;
  padding: 32px;
  border: 1px solid var(--border, #E9EDF2);
  border-radius: 6px;
  background: var(--accent-light, rgba(5, 150, 105, 0.08));
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}
.skillp__cta p { margin: 0; font-size: 15.5px; font-weight: 500; max-width: 48ch; }

@media (max-width: 860px) {
  .skillp__stats { grid-template-columns: repeat(2, 1fr); }
  .skillp__cols { grid-template-columns: 1fr; }
}
</style>
