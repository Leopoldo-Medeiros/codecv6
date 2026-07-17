## Bug Report

A support dashboard is supposed to list tickets with `high` priority
first, then `medium`, then `low`. It compiles, it sorts, nothing crashes
— but the order on screen is `high`, `low`, `medium`, which support agents
keep complaining about.

The test below is already written and currently **fails**. Find the bug
in `sortByPriority()` and fix it.
