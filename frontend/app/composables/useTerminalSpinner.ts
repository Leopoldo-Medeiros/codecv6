/**
 * Braille-frame spinner (à la ora) for the terminal-style auth pages.
 * The interval is cleaned up automatically when the owning component
 * unmounts, so callers only need start()/stop().
 */
export const useTerminalSpinner = () => {
    const FRAMES = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'] as const

    const frame = ref<string>(FRAMES[0])
    let timer: ReturnType<typeof setInterval> | null = null

    const start = () => {
        if (timer) return
        let i = 0
        timer = setInterval(() => {
            i = (i + 1) % FRAMES.length
            frame.value = FRAMES[i] as string
        }, 80)
    }

    const stop = () => {
        if (timer) {
            clearInterval(timer)
            timer = null
        }
    }

    onUnmounted(stop)

    return { frame: readonly(frame), start, stop }
}
