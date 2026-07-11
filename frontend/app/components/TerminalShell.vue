<template>
  <div class="term-page">
    <div class="term-backdrop" aria-hidden="true"></div>

    <div class="term-stage">
      <div class="term-mascot" aria-hidden="true">
        <TerminalMascot :mood="mood" :eye-shift="eyeShift" />
      </div>

      <div class="term-window" role="main">
        <div class="term-titlebar">
          <div class="term-lights">
            <NuxtLink to="/" class="term-light term-light--red" aria-label="Close — back to home">
              <span aria-hidden="true">×</span>
            </NuxtLink>
            <span class="term-light term-light--yellow" aria-hidden="true"></span>
            <span class="term-light term-light--green" aria-hidden="true"></span>
          </div>
          <span class="term-title">{{ title }}</span>
          <span class="term-titlebar__spacer" aria-hidden="true"></span>
        </div>

        <div class="term-body" @click="$emit('body-click')">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  title: string
  /** mascot mood: idle | watch | hide | think | error | happy */
  mood?: string
  /** -1..1 — mascot eye offset while typing */
  eyeShift?: number
}>(), {
  mood: 'idle',
  eyeShift: 0,
})
defineEmits<{ 'body-click': [] }>()

// Stable keys let unhead dedupe these entries across login ↔ register
// navigations, so the stylesheet is not torn down and re-fetched.
useHead({
  link: [
    { key: 'gf-preconnect', rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { key: 'gf-preconnect-static', rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    {
      key: 'gf-jetbrains-mono',
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap',
    },
  ],
})
</script>

<!-- Unscoped on purpose: the .term-*/.tui-* classes style slot content
     (form fields and output lines rendered by login.vue / register.vue). -->
<style>
.term-page, .term-page *, .term-page *::before, .term-page *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.term-page {
  --t-bg: #0B1215;                 /* terminal background */
  --t-bg-raise: #101A1E;           /* input boxes */
  --t-chrome: #1E2428;             /* titlebar */
  --t-border: rgba(255, 255, 255, 0.08);
  --t-border-strong: rgba(255, 255, 255, 0.16);
  --t-text: #C9D1D9;               /* default output */
  --t-dim: #8B949E;                /* comments, labels */
  --t-green: #34D399;              /* prompt glyphs, success, focus */
  --t-green-soft: #7EE787;         /* typed values */
  --t-red: #FF7B72;                /* errors */
  --t-yellow: #E3B341;             /* warnings / strength */
  --t-cyan: #79C0FF;               /* flags, links */
  --t-mono: 'JetBrains Mono', 'SF Mono', Menlo, Consolas, monospace;
  --t-ease: cubic-bezier(0.16, 1, 0.3, 1);

  font-family: var(--t-mono);
  min-height: 100dvh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: #030810;
  position: relative;
  overflow-x: hidden;
}

/* ── Backdrop: calm glows + fine grid (static, no animation) ───── */
.term-backdrop {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    radial-gradient(ellipse 700px 480px at 22% 18%, rgba(5, 150, 105, 0.13), transparent 65%),
    radial-gradient(ellipse 640px 460px at 82% 86%, rgba(52, 211, 153, 0.07), transparent 65%);
}
.term-backdrop::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.028) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.028) 1px, transparent 1px);
  background-size: 44px 44px;
  mask-image: radial-gradient(ellipse at center, #000 25%, transparent 78%);
  -webkit-mask-image: radial-gradient(ellipse at center, #000 25%, transparent 78%);
}

/* ── Stage: mascot + window ────────────────────────────────────── */
.term-stage {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 620px;
  /* room for the mascot standing on the titlebar */
  margin-top: 120px;
}
.term-mascot {
  position: absolute;
  top: -138px;
  right: 36px;
  width: 150px;
  z-index: 0;
}

/* ── Window chrome ─────────────────────────────────────────────── */
.term-window {
  position: relative;
  z-index: 1;
  width: 100%;
  border-radius: 10px;
  border: 1px solid var(--t-border);
  background: var(--t-bg);
  box-shadow:
    0 0 0 1px rgba(0, 0, 0, 0.4),
    0 24px 80px rgba(0, 0, 0, 0.65),
    0 0 120px rgba(52, 211, 153, 0.06);
  overflow: hidden;
  animation: term-rise 0.5s var(--t-ease) both;
}
@keyframes term-rise {
  from { opacity: 0; transform: translateY(16px) scale(0.985); }
  to   { opacity: 1; transform: none; }
}

.term-titlebar {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  background: linear-gradient(180deg, #262C31, var(--t-chrome));
  border-bottom: 1px solid rgba(0, 0, 0, 0.45);
  user-select: none;
}
.term-lights { display: flex; gap: 8px; flex: 1; }
.term-titlebar__spacer { flex: 1; }
.term-light {
  width: 12px; height: 12px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}
.term-light--red    { background: #FF5F57; border: 1px solid #E0443E; }
.term-light--yellow { background: #FEBC2E; border: 1px solid #D89E24; }
.term-light--green  { background: #28C840; border: 1px solid #1DAD2B; }
.term-light--red span {
  font-size: 10px;
  line-height: 1;
  color: rgba(0, 0, 0, 0);
  transition: color 0.15s;
}
.term-lights:hover .term-light--red span { color: rgba(77, 0, 0, 0.85); }
.term-title {
  font-size: 12px;
  font-weight: 500;
  color: #9BA3AB;
  letter-spacing: 0.01em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.term-body {
  padding: 22px 26px 26px;
  font-size: 13.5px;
  line-height: 1.9;
  color: var(--t-text);
  min-height: 300px;
}

/* ── Output lines ──────────────────────────────────────────────── */
.term-line { white-space: pre-wrap; word-break: break-word; }
.term-line--dim { color: var(--t-dim); }
.term-line--error { color: var(--t-red); }
.term-line--ok { color: var(--t-green-soft); }

.term-glyph { color: var(--t-green); font-weight: 700; }
.term-glyph--err { color: var(--t-red); font-weight: 700; }
.term-cmd { color: #E6EDF3; font-weight: 500; }
.term-flag { color: var(--t-cyan); }
.term-dollar { color: var(--t-green); font-weight: 700; margin-right: 9px; }

/* Comment lines double as nav: `# new here? create an account` */
.term-comment { color: var(--t-dim); }
.term-comment a {
  color: var(--t-cyan);
  text-decoration: none;
  border-bottom: 1px dashed rgba(121, 192, 255, 0.4);
  transition: color 0.15s, border-color 0.15s;
}
.term-comment a:hover { color: #A5D6FF; border-bottom-style: solid; }

/* ── TUI form (gum/charm-style: obvious fields inside the terminal) ── */
.tui-box {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin: 14px 0 6px;
  padding: 18px;
  border: 1px solid var(--t-border-strong);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.015);
}

.tui-field { cursor: text; }
.tui-field__head {
  display: flex;
  align-items: baseline;
  gap: 10px;
  margin-bottom: 6px;
}
.tui-field__label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--t-dim);
  cursor: text;
}
.tui-field__hint { font-size: 11px; color: var(--t-dim); opacity: 0.75; }
.tui-field__ok { color: var(--t-green); font-weight: 700; font-size: 12px; }
.tui-field__reveal {
  margin-left: auto;
  background: none;
  border: none;
  padding: 0;
  font: inherit;
  font-size: 11.5px;
  color: var(--t-dim);
  cursor: pointer;
  transition: color 0.15s;
}
.tui-field__reveal:hover { color: var(--t-cyan); }

.tui-field__box {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  background: var(--t-bg-raise);
  border: 1px solid var(--t-border-strong);
  border-radius: 6px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.tui-field__box:focus-within {
  border-color: var(--t-green);
  box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.12);
}
.tui-field__prefix {
  color: var(--t-green);
  font-weight: 700;
  user-select: none;
}
.tui-field__input {
  flex: 1;
  min-width: 0;
  border: none;
  outline: none;
  background: transparent;
  font: inherit;
  color: var(--t-green-soft);
  caret-color: var(--t-green);
  padding: 0;
}
.tui-field__input::placeholder { color: rgba(139, 148, 158, 0.4); }
.tui-field__input::selection { background: rgba(52, 211, 153, 0.35); }
.tui-field__input:disabled { opacity: 0.6; }
/* keep browser autofill from painting a white box over the terminal */
.tui-field__input:-webkit-autofill,
.tui-field__input:-webkit-autofill:hover,
.tui-field__input:-webkit-autofill:focus {
  -webkit-text-fill-color: var(--t-green-soft);
  -webkit-box-shadow: 0 0 0 100px var(--t-bg-raise) inset;
  transition: background-color 9999s ease-out;
}

/* Buttons */
.tui-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  padding: 12px 16px;
  border-radius: 6px;
  font: inherit;
  font-weight: 700;
  font-size: 13.5px;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.2s, border-color 0.2s, color 0.2s, transform 0.15s;
}
.tui-btn--primary {
  background: #059669;
  border: 1px solid #059669;
  color: #fff;
}
.tui-btn--primary:hover:not(:disabled) { background: #0BAB79; border-color: #0BAB79; transform: translateY(-1px); }
.tui-btn--primary:disabled { opacity: 0.45; cursor: not-allowed; }
.tui-btn--primary kbd {
  font: inherit;
  font-size: 11.5px;
  background: rgba(255, 255, 255, 0.18);
  border-radius: 4px;
  padding: 1px 7px;
}
.tui-btn--ghost {
  background: transparent;
  border: 1px solid var(--t-border-strong);
  color: var(--t-text);
  font-weight: 500;
}
.tui-btn--ghost:hover { border-color: rgba(52, 211, 153, 0.5); background: rgba(52, 211, 153, 0.05); }
.tui-btn--ghost svg { width: 16px; height: 16px; flex-shrink: 0; }

/* "# or" divider inside the box */
.tui-sep {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--t-dim);
  font-size: 12px;
}
.tui-sep::before, .tui-sep::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--t-border);
}

/* Strength meter */
.term-strength { letter-spacing: 1px; font-size: 12px; }
.term-strength--1 { color: var(--t-red); }
.term-strength--2 { color: var(--t-yellow); }
.term-strength--3 { color: var(--t-green); }
.term-strength--4 { color: var(--t-green-soft); }

/* Spinner + transitions */
.term-spinner { color: inherit; display: inline-block; width: 1ch; }

.term-fade-enter-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.term-fade-enter-from { opacity: 0; transform: translateY(3px); }

/* Blinking block cursor (decorative — command line only) */
.term-cursor {
  display: inline-block;
  width: 8px;
  height: 1.15em;
  vertical-align: text-bottom;
  background: var(--t-green);
  margin-left: 1px;
  animation: term-blink 1.05s steps(1) infinite;
}
@keyframes term-blink {
  0%, 49% { opacity: 1; }
  50%, 100% { opacity: 0; }
}

/* Staggered entrance for the static lines — content is fully present in
   the prerendered HTML (works with no JS); the animation is pure CSS on
   load. Pages opt lines in with .term-in-N; dynamic lines (error, spinner,
   success) don't carry these classes, so their feedback is never delayed. */
.term-in-1, .term-in-2, .term-in-3, .term-in-4, .term-in-5, .term-in-6 {
  animation: term-line-in 0.45s ease both;
}
.term-in-1 { animation-delay: 0.05s; }
.term-in-2 { animation-delay: 0.16s; }
.term-in-3 { animation-delay: 0.3s; }
.term-in-4 { animation-delay: 0.42s; }
.term-in-5 { animation-delay: 0.54s; }
.term-in-6 { animation-delay: 0.66s; }
@keyframes term-line-in {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: none; }
}

/* sr-only helper */
.term-sr {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
  white-space: nowrap;
  border: 0;
}

@media (max-width: 560px) {
  .term-body { padding: 18px 14px 22px; font-size: 12.5px; }
  .term-title { display: none; }
  .term-stage { margin-top: 96px; }
  .term-mascot { width: 118px; top: -108px; right: 16px; }
  .tui-box { padding: 14px; }
}
@media (prefers-reduced-motion: reduce) {
  .term-page *, .term-page *::before, .term-page *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
