---json
{
    "order": 1,
    "title": "Modern PHP: Types, Nullables and Enums",
    "type": "reading",
    "description": "PHP 8+ introduced an expressive type system that eliminates an entire class of bugs before the code even runs. In this module you will understand typed properties, union types, intersection types, string/int-backed enums, and how strict_types mode changes the interpreter's behaviour. The difference between PHP code from 2015 and 2024 starts here.",
    "tldr": "PHP 8 turned a famously loose language into one with a real type system. Strict types catch a huge class of bugs at the call site; union, nullable and intersection types describe intent; enums replace the magic-string constants that have haunted PHP codebases for two decades.",
    "difficulty": "intermediate",
    "estimated_minutes": 18,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": true,
    "playground_starter_code": "<?php\ndeclare(strict_types=1);\n\nenum OrderStatus: string {\n    case Pending  = 'pending';\n    case Paid     = 'paid';\n    case Refunded = 'refunded';\n}\n\nfunction describe(OrderStatus $status): string {\n    return match ($status) {\n        OrderStatus::Pending  => 'Awaiting payment',\n        OrderStatus::Paid     => 'Paid in full',\n        OrderStatus::Refunded => 'Refunded to customer',\n    };\n}\n\necho describe(OrderStatus::Paid);",
    "challenge_prompt": null,
    "resources": [
        {
            "url": "https://www.php.net/manual/en/language.types.declarations.php",
            "label": "PHP 8.3 Type System"
        },
        {
            "url": "https://www.php.net/manual/en/language.enumerations.php",
            "label": "PHP Enums — official docs"
        },
        {
            "url": "https://wiki.php.net/rfc/typed_properties_v2",
            "label": "Typed Properties RFC"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Form payload sanitizer — write sanitizeSignup(array $raw): array that takes $_POST-style input and returns typed fields: email (trimmed string), age (int, 0 if missing), terms_accepted (bool from 'yes'/'1'/'true'), referral_code (?string). This is what every onboarding controller does on day one.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\n/**\n * Sanitize a raw $_POST-style array into typed fields.\n *   - email          → trimmed string\n *   - age            → int (0 if missing)\n *   - terms_accepted → bool, true when value is 'yes' / '1' / 'true' (case-insensitive)\n *   - referral_code  → string or null\n */\nfunction sanitizeSignup(array $raw): array\n{\n    // TODO: implement\n    return [];\n}\n\n// Try your implementation against these:\nvar_dump(sanitizeSignup([\n    'email' => '  alice@example.com  ',\n    'age' => '25',\n    'terms_accepted' => 'yes',\n    'referral_code' => 'WELCOME10',\n]));\nvar_dump(sanitizeSignup([]));\nvar_dump(sanitizeSignup(['terms_accepted' => 'no']));"
        },
        {
            "id": 2,
            "text": "Strict types refactor — a teammate wrote function calculateDiscount($price, $percent) { return $price - ($price * $percent / 100); }. Add declare(strict_types=1), parameter types, and a return type. Then call it with ('10.50', 10) and explain what changes between strict and non-strict mode.",
            "starter_code": "<?php\n// Step 1: add declare(strict_types=1); on the line above this comment.\n\n// Step 2: add parameter types and a return type to this function.\nfunction calculateDiscount($price, $percent)\n{\n    return $price - ($price * $percent / 100);\n}\n\n// Step 3: run BOTH calls and see what changes between strict and non-strict mode.\necho calculateDiscount(10.50, 10) . PHP_EOL;     // 9.45\necho calculateDiscount('10.50', 10) . PHP_EOL;   // ← with strict_types this throws TypeError"
        },
        {
            "id": 3,
            "text": "Security: type juggling bypass — explain why if ($_GET['role'] == 'admin') is exploitable when a request is crafted as ?role=0 in PHP 7. Fix it with strict equality and a whitelist. Classic technical-screen question for senior backend roles.",
            "starter_code": "<?php\n// The vulnerable controller below is in production. Two things to do:\n//\n//  1. In a comment, explain WHY this returns true when an attacker sends\n//     ?role=0 under PHP 7 semantics. (Hint: 'admin' == 0)\n//\n//  2. Rewrite isAdmin() using strict equality and a whitelist of allowed\n//     roles. Make it impossible to bypass with crafted input.\n\nfunction isAdmin(array $request): bool\n{\n    return $request['role'] == 'admin'; // ← exploitable\n}\n\n// Crafted requests — your fixed version must return false for both.\nvar_dump(isAdmin(['role' => 0]));        // current code: true (BUG)\nvar_dump(isAdmin(['role' => 'admin']));  // legitimate admin: true\nvar_dump(isAdmin(['role' => 'user']));   // regular user: false"
        },
        {
            "id": 4,
            "text": "Convert magic strings to an enum — find any model in your project that has a status column stored as a raw string. Define a backed enum, cast the property, and update one match() that checks the status to be exhaustive.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\n// Define a string-backed enum OrderStatus with three cases:\n//   Pending = 'pending', Paid = 'paid', Refunded = 'refunded'\n//\n// Then add a method isFinal(): bool that uses an exhaustive match()\n// — Paid and Refunded are final, Pending is not.\n\nenum OrderStatus: string\n{\n    // TODO: cases\n}\n\n// Once defined, this should compile and run.\nfunction describe(OrderStatus $status): string\n{\n    return match ($status) {\n        // TODO: cover every case exhaustively — drop any case here and\n        // your IDE/PHPStan should immediately flag it as unhandled.\n    };\n}\n\n// echo describe(OrderStatus::Paid);\n// var_dump(OrderStatus::Pending->isFinal());   // false\n// var_dump(OrderStatus::Refunded->isFinal());  // true"
        },
        {
            "id": 5,
            "text": "Money math — a junior writes $total = 0.1 + 0.2 and asserts === 0.3. Explain why the assertion fails. Then implement sumCents(array $stringPrices): int that takes ['9.99', '1.50', '0.01'] and returns the total in cents (integer) — the only safe way to handle money in any language.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\n// Part 1 — convince yourself the float assertion fails.\nvar_dump(0.1 + 0.2);                  // 0.30000000000000004\nvar_dump(0.1 + 0.2 === 0.3);          // false  ← IEEE 754 talking\n\n// Part 2 — implement sumCents.\n// Take prices as strings (the way HTTP forms and CSV files give them),\n// convert each to integer cents (e.g. '9.99' → 999), and sum.\nfunction sumCents(array $stringPrices): int\n{\n    // TODO: implement — avoid floats entirely if you can.\n    return 0;\n}\n\necho sumCents(['9.99', '1.50', '0.01']) . PHP_EOL;   // expected: 1150\necho sumCents(['100.00', '0.99']) . PHP_EOL;         // expected: 10099\necho sumCents([]) . PHP_EOL;                          // expected: 0"
        }
    ],
    "prerequisites": [
        {
            "id": 1,
            "title": "PHP installation & first script"
        },
        {
            "id": 2,
            "title": "Variables, control flow and functions"
        }
    ],
    "concepts": [
        "strict_types",
        "union-types",
        "nullable",
        "intersection-types",
        "enums",
        "type-juggling"
    ],
    "quiz": null,
    "evidence": null
}
---
## Core (PHP fundamentals)

### strict_types: opting out of type juggling

Add this single line at the top of every PHP file you write:

```php
declare(strict_types=1);
```

It turns off automatic type coercion for the *current file*'s function calls. Without it, PHP silently converts `'42'` to `42` when you call a function expecting `int`. With it, you get a `TypeError` at the boundary — exactly where the bug is, not three layers deep where the wrong type finally explodes.

```php
declare(strict_types=1);

function tax(int $cents): int {
    return (int) round($cents * 0.23);
}

tax(150);     // 35
tax('150');   // TypeError — strict types blocks the implicit cast
```

This is the single highest-impact line you can add to a PHP project that didn't have it.

### Parameter, return and property types

PHP supports type declarations on **parameters**, **return values**, and (since 7.4) **class properties**:

```php
class Invoice {
    public int $number;
    public float $total = 0.0;

    public function addItem(string $sku, int $qty, float $price): void {
        $this->total += $qty * $price;
    }

    public function format(): string {
        return sprintf('#%d — €%.2f', $this->number, $this->total);
    }
}
```

The `void` return type means "this function returns nothing useful". Use it explicitly to make intent unambiguous to both readers and the IDE.

### Nullable shorthand: ?string

Real data has gaps. A user might not have a `linkedin_url`. The `?` prefix means "this OR null":

```php
function profileUrl(?string $handle): ?string {
    if ($handle === null) return null;
    return "https://linkedin.com/in/{$handle}";
}
```

`?string` is sugar for `string|null`. Both work; `?` reads cleaner for the common nullable case.

### Union types: one of several

PHP 8 added the `|` syntax for parameters that legitimately accept multiple types:

```php
function formatId(int|string $id): string {
    return is_int($id) ? "INT-{$id}" : "STR-{$id}";
}

formatId(42);       // "INT-42"
formatId('abc');    // "STR-abc"
```

Use unions sparingly. If you find yourself writing `int|string|float|bool`, that's a smell — your function is doing too much.

## Deeper dive (intermediate territory)

### Intersection types: combining contracts

Where unions say "one of these", intersections say "all of these at once". Useful when a parameter must implement multiple interfaces simultaneously:

```php
function dumpSize(Countable&Stringable $value): string {
    return count($value) . ': ' . $value;
}
```

The parameter must implement **both** `Countable` and `Stringable`. The compiler enforces it; no runtime checks needed. Intersections compose well with the standard SPL interfaces (`Iterator`, `Countable`, `Stringable`, `JsonSerializable`) and your own contracts.

### Enums: the death of magic strings

Before PHP 8.1, status fields were stored as untyped strings or class constants. Both invited typos:

```php
// ❌ Pre-enum: a typo silently becomes a wrong state
if ($order->status === 'refunded') { /* ... */ }
```

Enums replace this with a *closed set* of values the type system knows about:

```php
enum OrderStatus: string {
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Refunded = 'refunded';

    public function isFinal(): bool {
        return match ($this) {
            self::Paid, self::Refunded => true,
            self::Pending              => false,
        };
    }
}

function notify(OrderStatus $status): void {
    if ($status === OrderStatus::Refunded) { /* ... */ }
}
```

Three real wins:
- **No typos** — `OrderStatus::Refundd` won't compile.
- **Exhaustive `match`** — add a new case and the compiler points at every `match` that doesn't handle it.
- **Methods on the enum** — `isFinal()` lives next to the data, not scattered across helpers.

### Backed enums for persistence

Backed enums (`: string` or `: int`) serialise to the database and back. Use `OrderStatus::from('paid')` to hydrate, `OrderStatus::tryFrom($value)` when the input might be invalid:

```php
$status = OrderStatus::tryFrom($row['status']) ?? OrderStatus::Pending;
```

String-backed for human-readable storage. Int-backed when you need compact indexed columns (e.g. a high-write events table). Laravel's `Eloquent` casts (`'status' => OrderStatus::class`) make the model property a real enum without lifting a finger.

### Property covariance and variance gotchas

PHP allows return-type **covariance** (a child can narrow the return) but parameter types are **invariant** in most cases. This bites when refactoring inheritance:

```php
class Repository {
    public function find(int $id): ?Model { /* ... */ }
}

class UserRepository extends Repository {
    public function find(int $id): ?User { /* OK — covariant return */ }
}
```

But you cannot widen a parameter in a child — that breaks LSP and PHP rightly refuses. When you hit one of these errors, the fix is almost always to redesign the contract rather than fight the type system.

## Senior insights (architecture & interview prep)

### Code-review red flags

Things to call out the next time you review a teammate's PR:

- **`mixed` everywhere.** A function returning `mixed` is a function that gave up on types. Push for a union or a value-object wrapper.
- **`@param string $foo` PHPDoc instead of a real type.** PHPDoc is hints; only the declaration is enforced. If the parameter can be typed, type it.
- **`stdClass` instead of a DTO.** "We'll just decode JSON to stdClass for now" is how every PHP codebase ends up with untyped property access bugs in production. Decode into a typed class.
- **`null` returned from a method whose name promises a value.** `getUser(): ?User` is sometimes right, but `currentUser(): ?User` smells — call sites end up with `if ($user === null)` everywhere. A `requireUser(): User` that throws is often the right contract.
- **A method that accepts `string|int|array`.** That's a function doing three jobs. Split it.

### When to adopt strict_types in a legacy codebase

Strict types is per-file. You don't have to migrate everything at once — that's a feature, not a workaround. A pragmatic rollout:

1. **Add it to new files only.** Set a lint rule (`php-cs-fixer` `declare_strict_types`) so every new PHP file ships with `declare(strict_types=1)` at the top.
2. **Migrate file-by-file when you touch a file for another reason.** Boy-scout rule: leave it cleaner than you found it. Don't open dedicated "add strict types to module X" PRs — they're large, mechanical, and high-blast-radius.
3. **Run the test suite after each batch.** Strict types surfaces real bugs (typically: `$_GET`/`$_POST` strings being passed unchanged to int-typed functions). The test suite is your safety net.

If a function genuinely needs to accept loose input (e.g. an HTTP request handler before validation), normalise at the boundary — don't disable strict types for the entire file.

### Trade-offs to discuss with your team

- **Enums vs. database-friendly constants.** Enums break older Laravel ecosystems that didn't expect them; some packages serialise them inconsistently. Keep an `->value` accessor handy.
- **Union types vs. polymorphism.** A union of two types in one function is often a missed opportunity for two methods or a small interface. Use unions when the two types have legitimately the same processing path.
- **Performance.** Type declarations cost nothing at runtime — opcache caches the parsed AST. Don't let anyone tell you otherwise.

### What interviewers listen for

Common technical-screen prompts and the bullet they're waiting for:

- *"How do PHP comparisons work?"* — Mention strict (`===`) vs loose (`==`), then go straight to the security angle: type-juggling in `==` is exploitable when comparing user input against tokens or roles.
- *"What's the difference between an abstract class and an interface?"* — Senior answer: interfaces describe a contract; abstract classes share *implementation*. PHP 8 added interface intersection types — interfaces have grown more expressive, abstract classes less necessary.
- *"How would you refactor a function that returns `mixed`?"* — Start by enumerating the actual return shapes the function produces. Replace with a union, a value object, or a sealed hierarchy of result types. Show that you reach for types as a *design* tool, not a syntax tax.

The senior bar isn't memorising every union/intersection rule. It's reaching for the type system as a way to make wrong code unrepresentable.
