/**
 * Minimal Markdown → HTML renderer shared across step content and the
 * public challenge teaser. Intentionally small (headings, code, tables,
 * bold, inline code, lists, paragraphs) — the content it renders is
 * author-controlled challenge/step copy, not arbitrary user input.
 */
export function renderMarkdown(text: string): string {
    return text
        .replace(/```(\w*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>')
        .replace(/^### (.+)$/gm, '<h3>$1</h3>')
        .replace(/^## (.+)$/gm, '<h2>$1</h2>')
        .replace(/^# (.+)$/gm, '<h1>$1</h1>')
        .replace(/^\| (.+) \|$/gm, (_, row) => {
            const cells = row.split(' | ').map((c: string) => `<td class="border border-gray-200 dark:border-gray-700 px-2 py-1 text-xs">${c}</td>`).join('')
            return `<tr>${cells}</tr>`
        })
        .replace(/(<tr>[\s\S]*?<\/tr>\n?)+/g, match => `<table class="w-full border-collapse my-3">${match}</table>`)
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/`([^`\n]+)`/g, '<code>$1</code>')
        .replace(/^\* (.+)$/gm, '<li>$1</li>')
        .replace(/^- (.+)$/gm, '<li>$1</li>')
        .replace(/(<li>[\s\S]*?<\/li>\n?)+/g, match => `<ul>${match}</ul>`)
        .replace(/\n\n/g, '</p><p>')
}
