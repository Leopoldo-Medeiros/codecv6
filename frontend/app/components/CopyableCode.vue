<template>
  <div class="group relative my-3">
    <button
      class="absolute right-2 top-2 z-10 flex items-center gap-1 rounded-md border border-neutral-700 bg-neutral-900/90 px-2 py-1 text-[10px] font-medium text-neutral-400 opacity-0 transition-opacity hover:text-neutral-200 focus:opacity-100 group-hover:opacity-100"
      :aria-label="copied ? 'Copied' : 'Copy code'"
      @click="copy"
    >
      <Check v-if="copied" :size="11" class="text-emerald-400" />
      <Copy v-else :size="11" />
      {{ copied ? 'Copied' : 'Copy' }}
    </button>
    <pre class="overflow-x-auto rounded-lg border border-neutral-800 bg-neutral-950 px-4 py-3 text-xs leading-relaxed text-neutral-300"><code>{{ code }}</code></pre>
  </div>
</template>

<script setup lang="ts">
import { Check, Copy } from 'lucide-vue-next'

const props = defineProps<{
  code: string
}>()

const copied = ref(false)
let timer: ReturnType<typeof setTimeout> | null = null

async function copy() {
  try {
    await navigator.clipboard.writeText(props.code)
    copied.value = true
    if (timer) clearTimeout(timer)
    timer = setTimeout(() => { copied.value = false }, 1600)
  } catch {
    /* clipboard unavailable (http origin) — button simply does nothing */
  }
}

onUnmounted(() => { if (timer) clearTimeout(timer) })
</script>
