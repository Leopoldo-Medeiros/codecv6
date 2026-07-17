## Bug Report

The client list is supposed to display names as `"Last, First"` — the
standard format for sorted directories. `formatFullName()` builds the
string, but every entry comes out as `"First, Last"` instead.

The test below is already written and currently **fails**. Find the bug
and fix it — no new variables needed, just reorder something.
