<template>
  <div
    class="relative overflow-hidden rounded-xl border border-slate-800/60"
    style="background-image: radial-gradient(circle, #1e293b 1px, transparent 1px); background-size: 20px 20px; background-color: #020b18;"
  >
    <div v-if="renderError" class="p-4 font-mono text-xs text-rose-400">
      {{ renderError }}
    </div>
    <div
      v-else
      ref="container"
      class="flex justify-center overflow-x-auto p-6"
    />
  </div>
</template>

<script setup lang="ts">
import mermaid from 'mermaid'

const props = defineProps<{ code: string }>()

const container = ref<HTMLElement>()
const renderError = ref<string>()

let counter = 0

mermaid.initialize({
  startOnLoad: false,
  theme: 'base',
  themeVariables: {
    background: 'transparent',
    primaryColor: '#0f2e1e',
    primaryTextColor: '#e2e8f0',
    primaryBorderColor: '#059669',
    secondaryColor: '#0f172a',
    tertiaryColor: '#1e293b',
    lineColor: '#334155',
    mainBkg: '#0f2e1e',
    nodeBorder: '#059669',
    clusterBkg: '#0f172a',
    titleColor: '#f1f5f9',
    edgeLabelBackground: '#0f172a',
    fontFamily: '"Inter", "Plus Jakarta Sans", system-ui, sans-serif',
    fontSize: '13px',
    // Decision / special nodes
    fillType0: '#0f2e1e',
    fillType1: '#0f172a',
    // Text
    textColor: '#94a3b8',
    labelColor: '#94a3b8',
    // Specific node types
    attributeBackgroundColorEven: '#0f172a',
    attributeBackgroundColorOdd: '#1e293b',
  },
})

async function render() {
  if (!container.value) return

  renderError.value = undefined

  try {
    const id = `mermaid-${++counter}`
    const { svg } = await mermaid.render(id, props.code)
    container.value.innerHTML = svg

    const svgEl = container.value.querySelector<SVGElement>('svg')
    if (svgEl) {
      svgEl.style.maxWidth = '100%'
      svgEl.style.height = 'auto'
      svgEl.style.background = 'transparent'

      // Make edge labels blend with dark background
      svgEl.querySelectorAll<SVGElement>('.edgeLabel .label').forEach(el => {
        el.style.background = '#0f172a'
        el.style.color = '#64748b'
      })
    }
  }
  catch (e: any) {
    renderError.value = e?.message ?? 'Failed to render diagram'
  }
}

onMounted(render)
watch(() => props.code, render)
</script>
