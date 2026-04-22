<template>
  <canvas ref="canvasRef" class="auth-bg" />
</template>

<script setup lang="ts">
const canvasRef = ref<HTMLCanvasElement | null>(null)

onMounted(() => {
  const canvas = canvasRef.value
  if (!canvas) return
  const ctx = canvas.getContext('2d')!
  let raf: number
  let angle = 0

  const N = 160
  const φ = Math.PI * (3 - Math.sqrt(5))
  const pts: Array<{ ox: number; oy: number; oz: number }> = Array.from({ length: N }, (_, i) => {
    const y = 1 - (i / (N - 1)) * 2
    const r = Math.sqrt(Math.max(0, 1 - y * y))
    return { ox: Math.cos(φ * i) * r, oy: y, oz: Math.sin(φ * i) * r }
  })

  const parts = Array.from({ length: 50 }, () => ({
    x: (Math.random() - 0.5) * 3.2,
    y: (Math.random() - 0.5) * 3.2,
    z: (Math.random() - 0.5) * 3.2,
    vx: (Math.random() - 0.5) * 0.0007,
    vy: (Math.random() - 0.5) * 0.0007,
    vz: (Math.random() - 0.5) * 0.0007,
    size: 0.7 + Math.random() * 1.1,
    op: 0.06 + Math.random() * 0.16,
  }))

  function resize() {
    canvas.width  = canvas.offsetWidth
    canvas.height = canvas.offsetHeight
  }

  function draw() {
    const W = canvas.width, H = canvas.height
    const cx = W / 2, cy = H / 2
    const R  = Math.min(W, H) * 0.3
    const D  = 750

    ctx.fillStyle = '#030810'
    ctx.fillRect(0, 0, W, H)

    angle += 0.0032

    const sinA = Math.sin(angle), cosA = Math.cos(angle)

    const proj = pts.map(p => {
      const rx = p.ox * cosA - p.oz * sinA
      const rz = p.oz * cosA + p.ox * sinA
      const ry = p.oy
      const pers = D / (D + rz * R)
      return {
        sx: cx + rx * R * pers,
        sy: cy - ry * R * pers,
        depth: (rz + 1) / 2,
        rx, ry, rz,
      }
    })

    // Faint sphere outline ring
    ctx.save()
    ctx.beginPath()
    ctx.arc(cx, cy, R, 0, Math.PI * 2)
    ctx.strokeStyle = 'rgba(52,211,153,0.04)'
    ctx.lineWidth = 1
    ctx.stroke()
    ctx.restore()

    // Connections between nearby nodes
    for (let i = 0; i < proj.length; i++) {
      for (let j = i + 1; j < proj.length; j++) {
        const a = proj[i], b = proj[j]
        const dx = a.rx - b.rx, dy = a.ry - b.ry, dz = a.rz - b.rz
        const d  = Math.sqrt(dx * dx + dy * dy + dz * dz)
        if (d < 0.46) {
          const alpha = (1 - d / 0.46) * 0.14 * ((a.depth + b.depth) * 0.5)
          ctx.beginPath()
          ctx.moveTo(a.sx, a.sy)
          ctx.lineTo(b.sx, b.sy)
          ctx.strokeStyle = `rgba(52,211,153,${alpha})`
          ctx.lineWidth = 0.55
          ctx.stroke()
        }
      }
    }

    // Dots — back to front
    const sorted = [...proj].sort((a, b) => a.rz - b.rz)
    for (const pt of sorted) {
      const r     = 0.7 + pt.depth * 2.4
      const alpha = 0.08 + pt.depth * 0.82

      if (pt.depth > 0.52) {
        const gR = r * 4.5
        const g  = ctx.createRadialGradient(pt.sx, pt.sy, 0, pt.sx, pt.sy, gR)
        g.addColorStop(0, `rgba(52,211,153,${alpha * 0.38})`)
        g.addColorStop(1, 'rgba(52,211,153,0)')
        ctx.beginPath()
        ctx.arc(pt.sx, pt.sy, gR, 0, Math.PI * 2)
        ctx.fillStyle = g
        ctx.fill()
      }

      ctx.beginPath()
      ctx.arc(pt.sx, pt.sy, r, 0, Math.PI * 2)
      ctx.fillStyle = pt.depth > 0.58
        ? `rgba(167,243,208,${alpha})`
        : `rgba(52,211,153,${alpha * 0.55})`
      ctx.fill()
    }

    // Ambient floating particles
    for (const p of parts) {
      p.x += p.vx; p.y += p.vy; p.z += p.vz
      if (Math.abs(p.x) > 1.6) p.vx *= -1
      if (Math.abs(p.y) > 1.6) p.vy *= -1
      if (Math.abs(p.z) > 1.6) p.vz *= -1
      const pers = D / (D + p.z * R * 1.4)
      const sx   = cx + p.x * R * 1.4 * pers
      const sy   = cy - p.y * R * 1.4 * pers
      const dep  = (p.z + 1) * 0.5
      if (dep > 0.05) {
        ctx.beginPath()
        ctx.arc(sx, sy, p.size * dep, 0, Math.PI * 2)
        ctx.fillStyle = `rgba(52,211,153,${p.op * dep})`
        ctx.fill()
      }
    }

    raf = requestAnimationFrame(draw)
  }

  resize()
  draw()

  const ro = new ResizeObserver(resize)
  ro.observe(canvas.parentElement ?? canvas)

  onUnmounted(() => {
    cancelAnimationFrame(raf)
    ro.disconnect()
  })
})
</script>

<style scoped>
.auth-bg {
  position: fixed;
  inset: 0;
  width: 100%;
  height: 100%;
  display: block;
  pointer-events: none;
  z-index: 0;
}
</style>
