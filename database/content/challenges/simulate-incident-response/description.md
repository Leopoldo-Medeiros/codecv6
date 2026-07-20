## The situation

Friday, 17:40. A data migration ran against production with a broken mapping.
The damage report so far: some users lost their email (`null`), a bad role
mapping minted roles that don't exist in the system, and nobody trusts the
users table anymore.

The incident commander splits the response into the four phases every
playbook shares — **detect** what's wrong, **plan** the recovery, **execute**
it, **verify** the result — and hands you the tooling ticket: build the
`IncidentResponse` class that drives all four. This is the capstone: nothing
here is new, but you have to compose all of it under one API.

## Your task

Implement the four phases of `IncidentResponse`. The system's valid roles are
`admin` and `user`.

- `detectAnomalies(array $users): void` — scan the records and remember every
  problem you find: `null` emails, roles outside the valid set, duplicate
  entries. This phase only *observes* — it mutates nothing.
- `generateRecoveryPlan(): array` — return the recovery steps (strings) for
  what detection found, in execution order. Found anomalies must produce a
  non-empty plan; a clean table produces an empty one.
- `executeRecovery(array &$users): array` — apply the fixes to the array
  (by reference — this is your "run the rollback SQL" moment) and report
  `['success' => bool, 'fixed' => int, 'remaining_issues' => int]`. Fixing a
  null email can mean assigning a placeholder that verification accepts.
- `verifyIntegrity(array $users): array` — independent post-check:
  `['valid' => int, 'invalid' => int, 'issues' => list<string>]`. A record is
  valid when it has a non-null email **and** a valid role.

## Examples

| **Input state** | **Expected behaviour** |
| one user with `email => null` | plan is non-empty; `executeRecovery` reports `fixed > 0` |
| a `role => 'superadmin'` | `verifyIntegrity` counts it in `invalid` |
| two clean users | `valid = 2`, `invalid = 0`, empty plan |

## Hints

- **Hint 1:** keep detection results in the `$anomalies` property — the plan and the recovery both read from what detection stored, which is what makes "detect first, act second" more than ceremony.
- **Hint 2:** `verifyIntegrity` must not depend on `detectAnomalies` having run — it's the independent auditor. Compute validity from the records it receives, nothing else.
- **Hint 3:** the `&$users` reference is the point of `executeRecovery`: modify the actual array (`foreach ($users as &$u)` or index writes), then count what you changed for the `fixed` report.

## In the real world

This is the shape of every real incident runbook: Datadog/PagerDuty call the
phases detection, triage, remediation, and postmortem verification. The
discipline this challenge encodes — *observe without mutating, plan before
touching data, verify with an independent check* — is exactly what separates
a controlled recovery from a second incident caused by the fix. It's also why
real migrations ship with a rollback script and a data-integrity check in the
same PR.
