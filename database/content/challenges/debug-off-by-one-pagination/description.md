## Bug Report

QA filed this ticket against the `paginate()` helper below:

> "Page 1 of the product list is missing the first 3 items. Every page
> seems to be showing items that belong on the *next* page instead."

The function looks reasonable at a glance — it compiles, it returns an
array, nothing crashes. The tests below are already written and they
currently **fail**. Read the function carefully, find the bug, and fix it
so all tests pass. Do not rewrite the function from scratch — the fix is
a small, targeted change.
