## Enum-Driven Permission Matrix

Enterprise applications have complex authorization rules: who can do what, under which conditions, to which resource. Encoding this in a switch statement scattered across controllers is unmaintainable. Encoding it in an enum keeps it all in one place and makes it queryable.

**Goal:** build a `Role` enum whose `can()` method encodes the permission matrix for a document management system:

| Action | `Viewer` | `Editor` | `Admin` |
|--------|----------|----------|---------|
| `read` | ✅ | ✅ | ✅ |
| `write` | ❌ | ✅ | ✅ |
| `delete` | ❌ | ❌ | ✅ |
| `share` | ❌ | ✅ | ✅ |
| `manage_users` | ❌ | ❌ | ✅ |

Also implement `Role::fromString()` that returns the correct case or throws `\ValueError` for unknown roles.
