## Bug Report

`sumRange()` recursively sums every integer from 1 to `$n`. For small
inputs it looks fine at a glance, but every result is off by exactly 1 —
too low. Recursive off-by-one bugs are notoriously hard to spot because
the recursive call itself looks completely correct; the bug is almost
always in the base case.

The tests below are already written and both currently **fail**. Find the
bug and fix it — the fix is a single character in the base case.
