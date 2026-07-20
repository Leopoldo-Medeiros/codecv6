## The situation

A user signed up as `Jane@Example.com` — phone keyboards love capitalizing
the first letter. Weeks later she logs in typing `jane@example.com`, like
everyone does, and gets **"account not found."** Support escalates it as
"login is broken"; the truth is one comparison operator deep.

`findUserByEmail()` does an exact, case-sensitive match. Email addresses are
case-insensitive by convention (technically the local part *may* be case
sensitive per RFC, but no real provider treats it that way — and your users
certainly don't).

## Your task

The tests are already written and one currently **fails**:

| **Lookup** | **Expected** |
| `'John@Example.com'` against stored `john@example.com` | finds John |
| `'jane@example.com'` with no such user | `null` |

Make the match case-insensitive without breaking the not-found path.

## Hints

- **Hint 1:** `strcasecmp($a, $b) === 0` is the direct tool; normalizing both sides with `strtolower()` reads even clearer.
- **Hint 2:** resist "fixing" it by lowercasing only the *input* — the stored value can carry mixed case too. Normalize both sides of the comparison.

## In the real world

The production-grade fix happens **at write time**: normalize the email to
lowercase when the account is created, add a case-insensitive unique index,
and then reads stay simple. MySQL's default collation happens to be
case-insensitive, which *hides* this bug until someone moves to Postgres
(case-sensitive by default) and login breaks in migration week — a genuinely
common war story. Laravel's `Str::lower()` in a mutator, or a `saving` hook,
is the one-line insurance.
