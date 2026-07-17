## Bug Report

A teammate wrote `hasPermission()` to check whether a user's permission
list contains a given permission. It shipped fine in testing — until a
user whose *first* permission was the one being checked got a "permission
denied" error in production.

> "It's like the function forgets the very first permission in the list
> exists."

The tests below are already written and currently **fail**. Find the bug
and fix it — the fix is a single operator, not a rewrite.
