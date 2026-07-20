/**
 * Splits a challenge description (Markdown, authored with the 5-part anatomy:
 * situation → task → examples → hints → in the real world) into the main
 * instructions body and the individual hints, so the workspace can render
 * hints behind progressive disclosure instead of inline spoilers.
 *
 * Descriptions without a `## Hints` section return an empty hints array and
 * the untouched markdown — legacy content keeps working.
 */
export interface ParsedChallengeDescription {
  instructions: string
  hints: string[]
}

export function parseChallengeDescription(markdown: string): ParsedChallengeDescription {
  const sections = markdown.split(/^(?=## )/m)
  const isHints = (s: string) => /^## Hints\b/i.test(s)

  const instructions = sections.filter(s => !isHints(s)).join('').trim()
  const hintSection = sections.find(isHints)

  const hints: string[] = []
  if (hintSection) {
    for (const line of hintSection.split('\n')) {
      const m = line.match(/^[-*]\s+(.+)$/)
      // Drop the authored "**Hint N:**" label — the UI numbers hints itself.
      if (m) hints.push(m[1]!.replace(/^\*\*Hint \d+[^*]*\*\*:?\s*/i, '').trim())
    }
  }

  return { instructions, hints }
}
