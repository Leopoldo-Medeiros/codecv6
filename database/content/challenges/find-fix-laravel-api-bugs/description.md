## The situation

You've inherited an e-commerce API mid-incident. Three customer-facing bugs
are open, there's no stack trace attached to any of them, and the reporter's
words are all you get — which is realistic: **users report symptoms, not
causes.** Your job is the full loop three times: translate the symptom into
a hypothesis, find the code, fix it, prove it with the test.

The reports:

- **Bug #1 — "Orders appear duplicated at checkout."** An order sometimes
  shows up twice in history. Look at what the checkout flow does with the
  order it creates.
- **Bug #2 — "Admin can delete their own account."** The delete endpoint
  checks the caller's role — and nothing else. Any user deleting *themselves*
  must get an error mentioning their "own account"; an admin deleting someone
  else must still succeed.
- **Bug #3 — "API returns 500 when a product is out of stock."** Downstream
  code trusts `isInStock()`; the crash is what happens when that answer is
  wrong for stock = 0.

## Your task

Fix all three. The classes map one-to-one to the bugs: the `checkout()`
flow + `OrderRepository` (#1), `UserController::deleteUser()` (#2), and
`Product::isInStock()` (#3).

| **Bug** | **Green looks like** |
| #1 | one checkout → `items` counted once |
| #2 | self-deletion returns an `error` containing "own account" (admin or not); admin deleting another user succeeds |
| #3 | `stock 0` → `false`, `stock 5` → `true` |

## Hints

- **Hint 1 (#1):** follow the order object through checkout line by line — how many times does it get handed to the repository? Duplicated *records* usually mean a duplicated *save*, not a duplicated request.
- **Hint 2 (#2):** the rule has two independent axes — *what you are* (role) and *who the target is* (you vs someone else). The bug is that only the first axis is checked. Compare the caller's id to the target id before role even matters.
- **Hint 3 (#3):** read `>= 0` out loud with stock equal to zero. Boundary operators (`>` vs `>=`) are where "works with normal data" quietly diverges from "works at the edge".

## In the real world

Each bug is a production archetype. #1 at real scale is the double-submit /
retry problem — solved with idempotency keys and `firstOrCreate`-style
guards, the same design as this project's Stripe webhook ledger. #2 is a
real rule in this codebase: `UserService` refuses self-deletion and protects
the last admin — authorization is never just "is admin?" but "is admin *and*
is this action on this target allowed?" — the reasoning that leads to
Laravel Policies. #3 is the off-by-one family in its business-logic costume.
The meta-skill is the loop you practiced: symptom → hypothesis → smallest
fix → test proves it — under pressure, that discipline is what keeps a
hotfix from becoming incident #4.
