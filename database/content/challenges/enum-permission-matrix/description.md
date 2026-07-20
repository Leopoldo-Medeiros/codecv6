## The situation

The document-management product just failed a customer's security review.
The finding: authorization rules are scattered across eleven controllers as
ad-hoc `if ($role === 'admin')` checks, three of which disagree about what an
Editor may do. The auditor's requirement is blunt: *"one authoritative,
reviewable place that answers: which role can perform which action."*

That artifact has a name — a **permission matrix** — and PHP 8.1 enums can
carry it as code that reads like the auditor's spreadsheet.

## Your task

Build the `Role` enum's behavior for a document system:

| **Action** | **Viewer** | **Editor** | **Admin** |
| `read` | yes | yes | yes |
| `write` | no | yes | yes |
| `delete` | no | no | yes |
| `share` | no | yes | yes |
| `manage_users` | no | no | yes |

- `can(string $action): bool` — encodes the matrix above.
- `fromString(string $role): self` — returns the matching case, throws
  `\ValueError` for unknown roles (`'superuser'` must blow up loudly, not
  default to something).

## Hints

- **Hint 1:** one readable shape is `match ($this) { Role::Admin => true, Role::Editor => in_array($action, ['read', 'write', 'share'], true), Role::Viewer => $action === 'read' }` — per-role allow-lists, admin as the catch-all row.
- **Hint 2:** the enum is string-backed, so `self::from($role)` already throws `\ValueError` on unknown input — `fromString` is a thin wrapper. (Know its sibling: `tryFrom` returns `null` instead. Choosing *throw vs null* is an API-design decision, and here the spec says throw.)
- **Hint 3:** unknown *actions* should deny, not throw — a `can()` that fails closed is the safe default in authorization code.

## In the real world

This is the smallest version of the layered reality: Spatie's
laravel-permission stores the matrix in the database, Laravel Policies put
per-model rules in classes — and this project uses both, plus a `RoleEnum`
for type-safe role identity. The principles survive every scale-up:
**centralize the matrix** (scattered checks are how the three-controllers-
disagree bug happens — this codebase's architecture review flags exactly
that), **fail closed**, and **make unknown roles an error**, because silently
defaulting an unrecognized role to "viewer" is how a typo'd seeder becomes a
security incident.
