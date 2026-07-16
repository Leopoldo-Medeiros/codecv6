## Bug Report

A user signed up with `Jane@Example.com`, then tried to log back in
typing `jane@example.com` (all lowercase, as most people do) — and got
"account not found." `findUserByEmail()` is doing an exact match, and
email addresses are effectively case-insensitive by convention.

The tests below are already written and one of them currently **fails**.
Find the bug and fix it.
