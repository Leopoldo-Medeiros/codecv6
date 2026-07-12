<template>
  <div
    class="prose prose-sm prose-slate dark:prose-invert max-w-none
           prose-headings:font-semibold
           prose-h1:text-xl prose-h2:text-lg prose-h3:text-base
           prose-p:leading-relaxed prose-p:text-gray-700 dark:prose-p:text-gray-300
           prose-code:rounded prose-code:bg-gray-100 prose-code:px-1 prose-code:py-0.5
           prose-code:text-emerald-700 prose-code:text-xs
           dark:prose-code:bg-gray-800 dark:prose-code:text-emerald-300
           prose-pre:border prose-pre:border-gray-200 prose-pre:bg-gray-50
           dark:prose-pre:border-gray-700 dark:prose-pre:bg-gray-900
           prose-strong:text-gray-900 dark:prose-strong:text-gray-100
           prose-a:text-emerald-600 dark:prose-a:text-emerald-400
           prose-li:text-gray-700 dark:prose-li:text-gray-300"
  >
    <template v-for="(segment, i) in segments" :key="i">
      <MermaidDiagram v-if="segment.type === 'mermaid'" :code="segment.content" class="not-prose my-4" />
      <LaravelLifecycleDiagramCard v-else-if="segment.type === 'lifecycle-diagram'" class="not-prose my-6" />
      <!-- eslint-disable-next-line vue/no-v-html -->
      <div v-else v-html="segment.content" />
    </template>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  content: string
}>()

// renderMarkdown moved to app/utils/markdown.ts (auto-imported) so the
// public challenge teaser can share it.

const segments = computed(() => {
  const result: Array<{ type: 'html' | 'mermaid' | 'lifecycle-diagram', content: string }> = []
  const blockPattern = /```(mermaid|lifecycle-diagram)\n([\s\S]*?)```/g
  let lastIndex = 0
  let match: RegExpExecArray | null

  while ((match = blockPattern.exec(props.content)) !== null) {
    if (match.index > lastIndex) {
      result.push({ type: 'html', content: renderMarkdown(props.content.slice(lastIndex, match.index)) })
    }
    result.push({ type: match[1] as 'mermaid' | 'lifecycle-diagram', content: (match[2] ?? '').trim() })
    lastIndex = match.index + match[0].length
  }

  if (lastIndex < props.content.length) {
    result.push({ type: 'html', content: renderMarkdown(props.content.slice(lastIndex)) })
  }

  return result
})
</script>
