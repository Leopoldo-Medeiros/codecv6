<template>
  <div>
    <!-- Month labels -->
    <div class="mb-1 flex gap-[3px] pl-[26px] text-[10px] text-gray-400 dark:text-slate-500">
      <div v-for="(lbl, i) in monthLabels" :key="i" class="w-[13px] shrink-0" :style="lbl ? '' : 'width:13px'">
        <span v-if="lbl" class="whitespace-nowrap">{{ lbl }}</span>
      </div>
    </div>

    <div class="flex gap-[3px]">
      <!-- Weekday labels -->
      <div class="mr-0.5 flex shrink-0 flex-col gap-[3px] pt-[0px] text-[9px] leading-none text-gray-400 dark:text-slate-500">
        <span class="h-[10px]" />
        <span class="h-[10px]">Mon</span>
        <span class="h-[10px]" />
        <span class="h-[10px]">Wed</span>
        <span class="h-[10px]" />
        <span class="h-[10px]">Fri</span>
        <span class="h-[10px]" />
      </div>

      <!-- Week columns -->
      <div class="flex gap-[3px] overflow-x-auto">
        <div v-for="(week, wi) in weeks" :key="wi" class="flex flex-col gap-[3px]">
          <div
            v-for="cell in week"
            :key="cell.date"
            class="h-[11px] w-[11px] rounded-[2px]"
            :class="cell.inRange ? levelClass(cell.level) : 'bg-transparent'"
            :title="cell.inRange ? `${cell.count} ${cell.count === 1 ? 'practice' : 'practices'} on ${cell.label}` : ''"
          />
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="mt-2 flex items-center justify-end gap-1 text-[10px] text-gray-400 dark:text-slate-500">
      <span class="mr-0.5">Less</span>
      <span v-for="l in [0, 1, 2, 3, 4]" :key="l" class="h-[11px] w-[11px] rounded-[2px]" :class="levelClass(l)" />
      <span class="ml-0.5">More</span>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  activity: Array<{ date: string, count: number }>
  weeks?: number
}>()

const WEEKS = props.weeks ?? 26

const counts = computed<Record<string, number>>(() => {
  const map: Record<string, number> = {}
  for (const a of props.activity) map[a.date] = a.count
  return map
})

function key(d: Date): string {
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}
function level(count: number): number {
  if (count <= 0) return 0
  if (count === 1) return 1
  if (count === 2) return 2
  if (count <= 4) return 3
  return 4
}
function levelClass(l: number): string {
  return [
    'bg-gray-100 dark:bg-slate-800',
    'bg-emerald-200 dark:bg-emerald-900/70',
    'bg-emerald-300 dark:bg-emerald-700/80',
    'bg-emerald-400 dark:bg-emerald-500',
    'bg-emerald-500 dark:bg-emerald-400',
  ][l] ?? 'bg-gray-100 dark:bg-slate-800'
}

interface Cell { date: string, count: number, level: number, label: string, inRange: boolean }

// Build week columns ending today, aligned so each column is Sun..Sat.
const grid = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  // End of the current week (Saturday) so the last column is complete.
  const end = new Date(today)
  end.setDate(end.getDate() + (6 - end.getDay()))
  const start = new Date(end)
  start.setDate(start.getDate() - (WEEKS * 7 - 1))

  const weeks: Cell[][] = []
  const cur = new Date(start)
  for (let w = 0; w < WEEKS; w++) {
    const col: Cell[] = []
    for (let d = 0; d < 7; d++) {
      const k = key(cur)
      const inRange = cur <= today
      const count = counts.value[k] ?? 0
      col.push({
        date: k,
        count,
        level: level(count),
        label: cur.toLocaleDateString('en-IE', { month: 'short', day: 'numeric' }),
        inRange,
      })
      cur.setDate(cur.getDate() + 1)
    }
    weeks.push(col)
  }
  return weeks
})

const weeks = computed(() => grid.value)

// Month label above the first column whose first day falls in a new month.
const monthLabels = computed(() => {
  const labels: string[] = []
  let lastMonth = -1
  for (const week of grid.value) {
    const first = week[0]
    const m = first ? new Date(first.date).getMonth() : -1
    if (m !== lastMonth && first) {
      labels.push(new Date(first.date).toLocaleDateString('en-IE', { month: 'short' }))
      lastMonth = m
    } else {
      labels.push('')
    }
  }
  return labels
})
</script>
