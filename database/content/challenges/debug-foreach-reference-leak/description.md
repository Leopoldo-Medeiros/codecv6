## Bug Report

`normalizeNames()` cleans up a list of user-submitted names — trims
whitespace, lowercases, then capitalizes the first letter. It has two
loops: one that does the normalization, and a second one that used to log
each name for debugging (the logging was removed, but the empty loop was
left behind — it looked harmless).

> "The last two names in the list keep coming out identical, no matter
> what we submit."

This is one of PHP's most notorious gotchas. The tests below are already
written and currently **fail**. Find the bug and fix it — the fix is a
single line, and it is not inside either loop's body.
