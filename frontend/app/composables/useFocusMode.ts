/**
 * Focus mode — hides the admin layout chrome (sidebar + topbar) so a
 * full-screen surface (e.g. the ChallengeEditor) gets the whole viewport.
 * Shared state so a page can toggle it and the layout can react.
 *
 * A page that enables it MUST disable it on unmount (see pages/step/[step_id]).
 */
export const useFocusMode = () => useState<boolean>('focus-mode', () => false)
