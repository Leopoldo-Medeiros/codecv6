<template>
  <svg
    ref="rootEl"
    class="bot"
    :class="`bot--${mood}`"
    :style="rootStyle"
    viewBox="0 0 220 250"
    fill="none"
    aria-hidden="true"
  >
    <defs>
      <linearGradient id="botRing" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" stop-color="#34D399" />
        <stop offset="0.55" stop-color="#059669" />
        <stop offset="1" stop-color="#79C0FF" />
      </linearGradient>
      <!-- 3D shading: light falls from the top-left -->
      <linearGradient id="botCasing" x1="0" y1="0" x2="0.35" y2="1">
        <stop offset="0" stop-color="#F4F6F8" />
        <stop offset="0.55" stop-color="#DDE1E6" />
        <stop offset="1" stop-color="#BEC6CE" />
      </linearGradient>
      <linearGradient id="botCasingSide" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="#D5DAE0" />
        <stop offset="1" stop-color="#AEB6BF" />
      </linearGradient>
      <radialGradient id="botScreen" cx="0.35" cy="0.3" r="1">
        <stop offset="0" stop-color="#131E26" />
        <stop offset="1" stop-color="#05090E" />
      </radialGradient>
    </defs>

    <ellipse class="bot__shadow" cx="110" cy="240" rx="54" ry="7" />

    <g class="bot__float">
      <!-- antenna -->
      <line class="bot__antenna" x1="110" y1="30" x2="110" y2="15" />
      <circle class="bot__antenna-tip" cx="110" cy="11" r="4.5" />

      <!-- ears -->
      <rect class="bot__side" x="20" y="74" width="13" height="30" rx="6.5" />
      <rect class="bot__side" x="187" y="74" width="13" height="30" rx="6.5" />

      <!-- legs -->
      <rect class="bot__side" x="86" y="198" width="15" height="28" rx="7.5" />
      <rect class="bot__side" x="119" y="198" width="15" height="28" rx="7.5" />

      <!-- side arms (relaxed) -->
      <rect class="bot__sidearm" x="57" y="156" width="14" height="42" rx="7" />
      <rect class="bot__sidearm" x="149" y="156" width="14" height="42" rx="7" />

      <!-- body -->
      <rect class="bot__casing" x="74" y="148" width="72" height="54" rx="17" />
      <circle class="bot__led" cx="98" cy="176" r="2.6" />
      <circle class="bot__led bot__led--2" cx="110" cy="176" r="2.6" />
      <circle class="bot__led bot__led--3" cx="122" cy="176" r="2.6" />

      <!-- head -->
      <g class="bot__head">
        <rect class="bot__casing" x="34" y="30" width="152" height="118" rx="30" />
        <!-- top-left highlight sells the rounded 3D shell -->
        <ellipse class="bot__gloss" cx="78" cy="44" rx="38" ry="10" />
        <rect class="bot__screen" x="47" y="43" width="126" height="92" rx="22" />
        <rect class="bot__ring" x="47" y="43" width="126" height="92" rx="22" />
        <!-- screen sheen -->
        <path class="bot__sheen" d="M55 52 Q100 40 165 50 L165 62 Q100 52 55 66 Z" />

        <!-- face -->
        <g class="bot__eyes" :style="eyesStyle">
          <template v-if="mood === 'error'">
            <path class="bot__x" d="M78 81l16 16M94 81l-16 16" />
            <path class="bot__x" d="M126 81l16 16M142 81l-16 16" />
          </template>
          <template v-else-if="mood === 'happy'">
            <path class="bot__arc" d="M76 93 Q86 79 96 93" />
            <path class="bot__arc" d="M124 93 Q134 79 144 93" />
            <path class="bot__smile" d="M100 110 Q110 119 120 110" />
          </template>
          <template v-else>
            <circle class="bot__eye" cx="86" cy="89" r="10" />
            <circle class="bot__eye" cx="134" cy="89" r="10" />
          </template>
        </g>

        <!-- hands (rise to cover the eyes while a password is typed) -->
        <g class="bot__hands">
          <g class="bot__hand bot__hand--l">
            <rect class="bot__casing" x="79" y="98" width="14" height="34" rx="7" />
            <circle class="bot__palm" cx="86" cy="90" r="14" />
            <path class="bot__fingers" d="M78 84c2-2 5-3 8-3M80 92h12M82 98c2 1 5 2 8 2" />
          </g>
          <g class="bot__hand bot__hand--r">
            <rect class="bot__casing" x="127" y="98" width="14" height="34" rx="7" />
            <circle class="bot__palm" cx="134" cy="90" r="14" />
            <path class="bot__fingers" d="M126 84c2-2 5-3 8-3M128 92h12M130 98c2 1 5 2 8 2" />
          </g>
        </g>
      </g>
    </g>
  </svg>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
  /** idle | watch | hide | think | error | happy */
  mood?: string
  /** -1..1 — horizontal eye offset while the user types (overrides mouse) */
  eyeShift?: number
}>(), {
  mood: 'idle',
  eyeShift: 0,
})

/* How far the eyes travel (px in viewBox space) and the head tilts (deg). */
const EYE_RANGE = { x: 8, y: 5 }
const TILT_RANGE = { x: 8, y: 6 }

const rootEl = ref<SVGSVGElement | null>(null)
// Normalized cursor position relative to the mascot's center, each -1..1.
const cursor = ref({ x: 0, y: 0 })

/* Mouse tracking — rAF-throttled, mouse pointers only, cleaned on unmount. */
let rafId = 0
function onPointerMove(e: PointerEvent) {
  if (e.pointerType !== 'mouse' || rafId) return
  rafId = requestAnimationFrame(() => {
    rafId = 0
    const el = rootEl.value
    if (!el) return
    const box = el.getBoundingClientRect()
    const cx = box.left + box.width / 2
    const cy = box.top + box.height / 2
    cursor.value = {
      x: Math.max(-1, Math.min(1, (e.clientX - cx) / (box.width * 1.2))),
      y: Math.max(-1, Math.min(1, (e.clientY - cy) / (box.height * 1.2))),
    }
  })
}

onMounted(() => {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return
  window.addEventListener('pointermove', onPointerMove, { passive: true })
})
onUnmounted(() => {
  window.removeEventListener('pointermove', onPointerMove)
  if (rafId) cancelAnimationFrame(rafId)
})

/* 3D presence: the whole robot tilts gently toward the cursor. */
const rootStyle = computed(() => ({
  transform: `perspective(700px) rotateY(${(cursor.value.x * TILT_RANGE.x).toFixed(2)}deg) rotateX(${(-cursor.value.y * TILT_RANGE.y).toFixed(2)}deg)`,
}))

/* Eyes: follow the typed text when the user is typing, else the cursor. */
const eyesStyle = computed(() => {
  const typing = props.mood === 'watch' && props.eyeShift !== 0
  const x = typing ? props.eyeShift * 9 : cursor.value.x * EYE_RANGE.x
  const y = typing ? 7 : cursor.value.y * EYE_RANGE.y
  return { transform: `translate(${x.toFixed(2)}px, ${y.toFixed(2)}px)` }
})
</script>

<style scoped>
.bot {
  display: block;
  width: 100%;
  height: auto;
  overflow: visible;
  transition: transform 0.18s ease-out;
  transform-style: preserve-3d;
  will-change: transform;
}

/* ── palette + 3D shading ────────────────────────────────── */
.bot__casing {
  fill: url(#botCasing);
  stroke: #9aa3ac;
  stroke-width: 1.5;
}
.bot__side {
  fill: url(#botCasingSide);
  stroke: #99a2ab;
  stroke-width: 1.5;
}
.bot__sidearm {
  fill: url(#botCasingSide);
  stroke: #99a2ab;
  stroke-width: 1.5;
  transition: opacity 0.25s;
}
.bot__gloss { fill: rgba(255, 255, 255, 0.55); }
.bot__screen { fill: url(#botScreen); }
.bot__sheen { fill: rgba(255, 255, 255, 0.05); pointer-events: none; }
.bot__ring {
  fill: none;
  stroke: url(#botRing);
  stroke-width: 2.5;
  filter: drop-shadow(0 0 7px rgba(52, 211, 153, 0.55));
  transition: stroke 0.3s;
}
.bot__shadow { fill: rgba(0, 0, 0, 0.45); }
.bot__antenna { stroke: #aab2ba; stroke-width: 3; stroke-linecap: round; }
/* no filter here on purpose: animating a filtered SVG node forces
   filter re-rasterization every frame */
.bot__antenna-tip {
  fill: #34d399;
  animation: bot-tip 2.6s ease-in-out infinite;
}
@keyframes bot-tip {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.35; }
}
.bot__led { fill: #34d399; opacity: 0.9; animation: bot-tip 2s ease-in-out infinite; }
.bot__led--2 { animation-delay: 0.3s; }
.bot__led--3 { animation-delay: 0.6s; }

/* ── float + shadow breathing ────────────────────────────── */
.bot__float { animation: bot-float 5s ease-in-out infinite; }
@keyframes bot-float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-7px); }
}
.bot__shadow { animation: bot-shadow 5s ease-in-out infinite; transform-box: fill-box; transform-origin: center; }
@keyframes bot-shadow {
  0%, 100% { transform: scaleX(1); opacity: 0.45; }
  50% { transform: scaleX(0.82); opacity: 0.3; }
}

/* ── eyes ────────────────────────────────────────────────── */
.bot__eyes { transition: transform 0.16s ease-out; }
.bot__eye {
  fill: #fff;
  transform-box: fill-box;
  transform-origin: center;
  animation: bot-blink 4.8s infinite;
}
@keyframes bot-blink {
  0%, 91%, 100% { transform: scaleY(1); }
  93%, 95% { transform: scaleY(0.08); }
}
.bot__arc, .bot__smile {
  stroke: #fff;
  stroke-width: 5;
  stroke-linecap: round;
  fill: none;
}
.bot__smile { stroke: #34d399; stroke-width: 4; }
.bot__x {
  stroke: #ff7b72;
  stroke-width: 5;
  stroke-linecap: round;
}

/* think — small scanning eyes while authenticating */
.bot--think .bot__eye {
  animation: bot-scan 0.9s ease-in-out infinite alternate;
  transform: scale(0.62);
}
@keyframes bot-scan {
  from { transform: scale(0.62) translateX(-14px); }
  to   { transform: scale(0.62) translateX(14px); }
}
.bot--think .bot__ring { filter: drop-shadow(0 0 12px rgba(52, 211, 153, 0.85)); }

/* hide — hands rise over the eyes (password entry) */
.bot__hands { pointer-events: none; }
.bot__hand {
  transform: translateY(118px);
  opacity: 0;
  transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s;
}
.bot__hand--r { transition-delay: 0.05s, 0s; }
.bot__palm {
  fill: url(#botCasing);
  stroke: #99a2ab;
  stroke-width: 1.5;
}
.bot__fingers {
  stroke: #99a2ab;
  stroke-width: 1.5;
  stroke-linecap: round;
  fill: none;
}
.bot--hide .bot__hand { transform: translateY(0); opacity: 1; }
.bot--hide .bot__sidearm { opacity: 0; }
.bot--hide .bot__eye { animation: none; transform: scaleY(0.1); }

/* error — red X eyes + head shake */
.bot--error .bot__ring {
  stroke: #ff7b72;
  filter: drop-shadow(0 0 8px rgba(255, 123, 114, 0.55));
}
.bot--error .bot__head { animation: bot-shake 0.45s ease; }
@keyframes bot-shake {
  0%, 100% { transform: translateX(0); }
  20% { transform: translateX(-7px); }
  40% { transform: translateX(6px); }
  60% { transform: translateX(-4px); }
  80% { transform: translateX(3px); }
}

/* happy — a little celebratory hop */
.bot--happy .bot__float { animation: bot-hop 0.7s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes bot-hop {
  0%, 100% { transform: translateY(0); }
  40% { transform: translateY(-18px); }
}
.bot--happy .bot__ring { filter: drop-shadow(0 0 14px rgba(52, 211, 153, 0.9)); }

@media (prefers-reduced-motion: reduce) {
  .bot { transition: none; }
  .bot__float, .bot__shadow, .bot__eye, .bot__antenna-tip, .bot__led { animation: none !important; }
  .bot__hand { transition: none; }
}
</style>
