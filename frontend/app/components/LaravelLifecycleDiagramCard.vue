<template>
  <!-- ── Inline preview card ───────────────────────────────────────── -->
  <div class="ll-card rounded-xl border p-6 flex items-start gap-6" style="background: #0d1526; border-color: #23304a;">
    <!-- Left accent -->
    <div class="shrink-0 w-1 self-stretch rounded-full" style="background: linear-gradient(to bottom, #34d399, #38bdf8);" />

    <div class="flex-1 min-w-0">
      <!-- Header -->
      <div class="flex items-center gap-2 mb-2">
        <span
          class="font-mono text-[10px] tracking-[0.2em] uppercase"
          style="color: #34d399;"
        >Framework Internals · 014</span>
      </div>
      <h3 class="text-white font-bold text-[17px] leading-snug mb-1.5">
        The Lifecycle of a Laravel Request
        <span style="color: #34d399;"> · </span>
        <span style="color: #fcd34d;">with Observability Injection</span>
      </h3>
      <p class="text-[13px] leading-relaxed mb-4" style="color: #8c9bb6;">
        13 stages — from
        <code class="font-mono text-xs px-1 py-0.5 rounded" style="color: #34d399; background: rgba(52,211,153,0.1);">public/index.php</code>
        to the HTTP Response, with observability injection (Tracer · Logger · Metrics · Errors) wired at every boundary.
      </p>

      <!-- Stage chips -->
      <div class="flex flex-wrap gap-1.5 mb-5">
        <span
          v-for="s in stages"
          :key="s.n"
          class="font-mono text-[10px] px-2 py-0.5 rounded-md border"
          :style="{ color: s.color, borderColor: s.border, background: s.bg }"
        >{{ s.n }}&nbsp;{{ s.label }}</span>
      </div>

      <!-- Legend + button -->
      <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 text-[11px] font-mono" style="color: #67768f;">
          <span class="flex items-center gap-1.5">
            <span class="inline-block w-2 h-px" style="background:#34d399;" />
            request path
          </span>
          <span class="flex items-center gap-1.5">
            <span class="inline-block w-2 h-px border-t border-dashed" style="border-color:#fbbf24;" />
            telemetry tap
          </span>
          <span class="flex items-center gap-1.5">
            <span class="inline-block w-2 h-px" style="background:#38bdf8;" />
            return path
          </span>
        </div>

        <button
          class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all"
          style="background: rgba(52,211,153,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.35);"
          @mouseenter="e => (e.target as HTMLElement).style.background = 'rgba(52,211,153,0.2)'"
          @mouseleave="e => (e.target as HTMLElement).style.background = 'rgba(52,211,153,0.12)'"
          @click="open = true"
        >
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <rect x="1" y="1" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.4" />
            <path d="M4.5 9.5L9.5 4.5M9.5 4.5H6M9.5 4.5V8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          View full diagram
        </button>
      </div>
    </div>
  </div>

  <!-- ── Fullscreen overlay ─────────────────────────────────────────── -->
  <Teleport to="body">
    <Transition name="ll-fade">
      <div
        v-if="open"
        class="fixed inset-0 z-[999] overflow-auto"
        style="background: #060911;"
        @keydown.esc="open = false"
        tabindex="-1"
        ref="overlayRef"
      >
        <!-- Close bar -->
        <div
          class="sticky top-0 z-10 flex items-center justify-between px-8 py-3 border-b"
          style="background: rgba(6,9,17,0.95); border-color: #1a2238; backdrop-filter: blur(8px);"
        >
          <span class="font-mono text-[11px] tracking-[0.2em] uppercase" style="color: #67768f;">
            Framework Internals · 014 &nbsp;·&nbsp; Laravel Request Lifecycle
          </span>
          <button
            class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
            style="color: #8c9bb6; border: 1px solid #23304a; background: transparent;"
            @mouseenter="e => (e.target as HTMLElement).style.color = '#e6edf7'"
            @mouseleave="e => (e.target as HTMLElement).style.color = '#8c9bb6'"
            @click="open = false"
          >
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
              <path d="M2 2L12 12M12 2L2 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            Close
          </button>
        </div>

        <!-- Full diagram -->
        <div class="overflow-x-auto">
          <LaravelLifecycleDiagram />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
const open = ref(false)
const overlayRef = ref<HTMLElement>()

watch(open, async (val) => {
  if (val) {
    document.body.style.overflow = 'hidden'
    await nextTick()
    overlayRef.value?.focus()
  }
  else {
    document.body.style.overflow = ''
  }
})

onUnmounted(() => {
  document.body.style.overflow = ''
})

const stages = [
  { n: '01', label: 'index.php',    color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '02', label: 'bootstrap',    color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '03', label: 'Kernel',       color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '04', label: 'Providers',    color: '#38bdf8', border: 'rgba(56,189,248,0.3)',  bg: 'rgba(56,189,248,0.08)' },
  { n: '05', label: 'IoC',          color: '#c084fc', border: 'rgba(192,132,252,0.3)', bg: 'rgba(192,132,252,0.08)' },
  { n: '06', label: 'Global MW',    color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '07', label: 'Router',       color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '08', label: 'Route MW',     color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '09', label: 'Controller',   color: '#34d399', border: 'rgba(52,211,153,0.4)',  bg: 'rgba(52,211,153,0.12)' },
  { n: '10', label: 'Eloquent',     color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '11', label: 'Render',       color: '#34d399', border: 'rgba(52,211,153,0.3)',  bg: 'rgba(52,211,153,0.08)' },
  { n: '12', label: 'Response MW',  color: '#38bdf8', border: 'rgba(56,189,248,0.3)',  bg: 'rgba(56,189,248,0.08)' },
  { n: '13', label: 'terminate()',  color: '#c084fc', border: 'rgba(192,132,252,0.3)', bg: 'rgba(192,132,252,0.08)' },
]
</script>

<style>
.ll-fade-enter-active,
.ll-fade-leave-active {
  transition: opacity 0.2s ease;
}
.ll-fade-enter-from,
.ll-fade-leave-to {
  opacity: 0;
}
</style>
