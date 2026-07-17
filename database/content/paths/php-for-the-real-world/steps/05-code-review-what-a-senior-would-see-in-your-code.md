---json
{
    "order": 5,
    "title": "Code Review: What a Senior Would See in Your Code",
    "type": "reading",
    "description": "You wrote the code, the tests pass, but the PR sits open for a week. This module covers what a senior actually looks at — naming, single responsibility, dependency direction, fat controllers, names that lie — and how to write reviews that change behaviour without being a jerk.",
    "tldr": "Tests passing is the floor, not the ceiling. A senior reads a diff and asks four questions: \"Does the name match the behaviour?\", \"Is anything doing more than one thing?\", \"Will the next developer be surprised?\", and \"What did this change break that we won't notice for three months?\". Internalise those questions and your PRs stop sitting open.",
    "difficulty": "intermediate",
    "estimated_minutes": 28,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": true,
    "playground_starter_code": "<?php\ndeclare(strict_types=1);\n\n// A real example of code that ships every week — and fails review for\n// at least five reasons. Refactor it in place. The four-question test:\n//   1. Does the name match the behaviour?           (process? user?)\n//   2. Is anything doing more than one thing?       (this method does six)\n//   3. Will the next developer be surprised?        (returns array OR throws OR commits)\n//   4. What did this change break for old callers?  (silent contract drift)\n\nclass UserController\n{\n    public function process($request)\n    {\n        $user = \\DB::table('users')->where('email', $request->email)->first();\n\n        if ($user) {\n            \\DB::table('users')->where('id', $user->id)->update([\n                'last_login' => date('Y-m-d H:i:s'),\n                'login_count' => $user->login_count + 1,\n            ]);\n\n            \\Mail::raw('You logged in.', function ($m) use ($user) {\n                $m->to($user->email)->subject('Welcome back');\n            });\n\n            \\Cache::forget('user_dashboard_' . $user->id);\n\n            return [\n                'status' => 'ok',\n                'user' => $user,\n                'token' => bin2hex(random_bytes(16)),\n            ];\n        }\n\n        return ['status' => 'error'];\n    }\n}\n\n// Suggested refactor sketch — five smaller methods, each named for its job:\n//   - findUserByEmail(string $email): ?User\n//   - recordLogin(User $user): void          (last_login + counter)\n//   - sendWelcomeBackEmail(User $user): void\n//   - invalidateDashboardCache(User $user): void\n//   - issueLoginToken(User $user): string\n// Then `login()` (NOT `process()`) orchestrates them.",
    "challenge_prompt": null,
    "resources": [
        {
            "url": "https://gist.github.com/wojteklu/73c6914cc446146b8b533c0988cf8d29",
            "label": "Clean Code — Robert C. Martin (summary)"
        },
        {
            "url": "https://github.com/alexeymezenin/laravel-best-practices",
            "label": "Laravel Best Practices"
        },
        {
            "url": "https://www.youtube.com/watch?v=_jDNAcej0JE",
            "label": "SOLID in PHP — practical examples"
        },
        {
            "url": "https://google.github.io/eng-practices/review/reviewer/",
            "label": "Google's code-review guide"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Refactor the lying name — given findOrCreateUser() that''s declared as getUser(), rename it AND find the three call sites in your own current project where this kind of name lies. Take 60 seconds per call site to decide whether the right fix is renaming the function or splitting it.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\n// The function is named getUser but creates one as a side effect.\n// Two acceptable refactors:\n//   (a) rename to findOrCreateUser — explicit about the side effect\n//   (b) split into two functions: findUser(): ?User and createGuest(int $id): User\n// Decide which one fits your codebase better and apply it.\n\nclass User { public int $id; public string $name; }\n\nfunction getUser(int $id): User\n{\n    $user = User::find($id);\n    if (! $user) {\n        $user = User::create(['id' => $id, 'name' => 'Guest']);\n    }\n    return $user;\n}\n\n// TODO: rename or split. Then audit your current project for three more\n// functions whose names don''t match their behaviour. Common offenders:\n//   - process(), handle(), do() in controllers\n//   - get*() methods that hit the DB and write\n//   - is*() / has*() methods with side effects"
        },
        {
            "id": 2,
            "text": "Slim down a fat controller — refactor the UserController::process() in the playground above. Extract at least three smaller methods, name them for their behaviour, and rewrite process() as a thin orchestrator. Note in a comment which of those new methods could be reused outside this controller.",
            "starter_code": null
        },
        {
            "id": 3,
            "text": "Cyclomatic complexity hunt — pick the most-branchy function in your current project (or use the discount example below). Count the ifs/&&/?:/match arms. If you go past 5, refactor with a match() table the way the Deeper dive section does.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\n// Cyclomatic complexity ≈ 7. Rewrite as a single match() table so the\n// discount rules become greppable data instead of nested branches.\n\nclass Order\n{\n    public int $total = 0;\n    public bool $userIsVip = false;\n}\n\nfunction calculateDiscount(Order $order): float\n{\n    if ($order->userIsVip) {\n        if ($order->total > 1000) {\n            return $order->total * 0.20;\n        } elseif ($order->total > 500) {\n            return $order->total * 0.15;\n        }\n        return $order->total * 0.10;\n    } else {\n        if (date('N') >= 6) {  // weekend\n            return $order->total * 0.05;\n        }\n    }\n    return 0;\n}\n\n// TODO: rewrite using `match (true)` with one arm per rule.\n// Bonus: extract the rules into a static array so future rules are\n// added without touching the function body."
        },
        {
            "id": 4,
            "text": "Write a real code-review comment — paste a 30–80 line piece of code you wrote in the last month into the playground. Comment on it as if you''re reviewing a stranger''s PR. Use BLOCKING / SUGGESTION / NIT tags. If you make more than 10 comments on your own 80 lines, you''ve learned something about your own habits.",
            "starter_code": null
        },
        {
            "id": 5,
            "text": "Surface a reversibility question — find a PR you opened in the last month. Write one paragraph answering: \"What did this change make harder to undo?\". This is the question that distinguishes senior reviewers from mid-level — train yourself to ask it before the merge button, not after.",
            "starter_code": null
        }
    ],
    "prerequisites": [
        {
            "id": 1,
            "title": "Laravel Request Lifecycle"
        },
        {
            "id": 2,
            "title": "Advanced Eloquent"
        }
    ],
    "concepts": [
        "code-review",
        "naming",
        "single-responsibility",
        "fat-controllers",
        "cyclomatic-complexity",
        "dependency-direction",
        "review-communication"
    ],
    "quiz": null,
    "evidence": null
}
---
## Core (foundations)

### Naming: the name is the contract

Half of all code-review feedback comes down to one question: *does the name match the behaviour?* When it doesn''t, the reader has to mentally translate every call site, which is where bugs hide.

```php
// ❌ Names that lie
function getUser(int $id): User
{
    $user = User::find($id);
    if (! $user) {
        $user = User::create(['id' => $id, 'name' => 'Guest']);  // ← surprise: it creates one
    }
    return $user;
}

// ✅ Name that matches behaviour
function findOrCreateUser(int $id): User { /* ... */ }
```

Rules of thumb:

- `get` / `find` should be cheap and side-effect free.
- `create` / `make` / `build` allocate something new.
- `process` / `handle` / `do` — these are red flags. They tell you nothing about what the function actually does.
- Boolean methods read like questions: `isPaid()`, `hasRefund()`, not `paid()` or `refund()`.

### One method, one job (Single Responsibility)

A method should have **one reason to change**. The reasons aren''t lines of code or branches — they''re the business reasons someone might modify it.

```php
// ❌ Three reasons to change: invoice format, email content, audit log shape.
public function bill(Order $order): void
{
    $pdf = $this->buildInvoicePdf($order);          // reason 1
    Mail::to($order->user)->send(new InvoiceMail($pdf));  // reason 2
    AuditLog::record('order.billed', ['order_id' => $order->id]);  // reason 3
}

// ✅ Three small methods, plus an orchestrator that does only the orchestration.
public function bill(Order $order): void
{
    $this->generateInvoice($order);
    $this->emailInvoice($order);
    $this->auditBilling($order);
}
```

The orchestrator is allowed to be longer — it''s describing the workflow, not implementing it. The implementations sit next to it where you can test each one in isolation.

### The "fat controller" anti-pattern

Controllers are HTTP adapters. Their job is to:

1. Translate request → typed input.
2. Hand it to a service / action / use-case.
3. Translate result → response.

That''s it. Anything else — database calls, mail sending, business rules — belongs elsewhere. When you see a controller method past 30 lines, that''s 30 lines that can''t be reused from a queue worker, a CLI command, or a test.

```php
// ❌ Fat controller
class CheckoutController extends Controller
{
    public function complete(Request $request)
    {
        // 80 lines: validates, charges Stripe, writes Order, emails receipt,
        // dispatches inventory job, updates user_stats…
    }
}

// ✅ Thin controller
class CheckoutController extends Controller
{
    public function complete(CompleteCheckoutRequest $request, CompleteCheckout $action): JsonResponse
    {
        $order = $action->handle($request->validated());

        return new OrderResource($order)->response()->setStatusCode(201);
    }
}
```

The `CompleteCheckout` action is also callable from `php artisan checkout:complete 42` and from a Stripe webhook handler. The fat controller version is callable from exactly one place.

### Names that lie — boolean returns

The single nastiest naming bug is a function whose **name implies a question** but whose **body has side effects**:

```php
public function hasInventory(Product $p): bool
{
    if (! Cache::has("stock.{$p->id}")) {
        Cache::put("stock.{$p->id}", $p->refreshStock());  // ← side effect
    }
    return Cache::get("stock.{$p->id}") > 0;
}
```

That cache prime is hiding inside what looks like a pure query. The next dev writes `if ($product->hasInventory()) { ... }` in a `Promise.all`-style parallel job and the cache stampedes. Split it: `refreshStockIfStale()` for the side effect, `hasInventory()` for the question.

## Deeper dive (intermediate)

### Cyclomatic complexity: just count the ifs

A senior eye can spot a method that''s too complex in about three seconds. The technical name is **cyclomatic complexity** — the number of independent paths through a function. The practical version: count your `if`, `match`, `&&`, `||`, and `?:`. More than 5 and you''re looking at a method that needs to be split.

```php
// CC ≈ 7 — every level of nesting is another path to test.
function calculateDiscount($order)
{
    if ($order->user->isVip()) {
        if ($order->total > 1000) {
            return $order->total * 0.20;
        } elseif ($order->total > 500) {
            return $order->total * 0.15;
        }
        return $order->total * 0.10;
    } else {
        if (now()->isWeekend()) {
            return $order->total * 0.05;
        }
    }
    return 0;
}
```

The refactored version pushes the decisions into a data structure:

```php
function calculateDiscount(Order $order): int
{
    $rules = match (true) {
        $order->user->isVip() && $order->total > 1000 => 0.20,
        $order->user->isVip() && $order->total > 500  => 0.15,
        $order->user->isVip()                          => 0.10,
        now()->isWeekend()                             => 0.05,
        default                                        => 0.00,
    };

    return (int) round($order->total * $rules);
}
```

Same six paths, but every path is on one line and the discount table is now greppable.

### Dependency direction: stable things don''t depend on unstable things

A core rule of architecture: **dependencies should point toward stability**. The hot, changes-every-week parts of your codebase (controllers, CLI commands, schedulers) should depend on the slow, rarely-changing parts (entities, domain services). Not the other way around.

Concretely:

- Your `Order` model should know nothing about HTTP requests.
- Your domain services should accept typed input, not `Illuminate\Http\Request`.
- Your business logic should not call `auth()->user()` — it should accept a `User` parameter.

When dependencies point the wrong way, the symptom is "I can''t test this without spinning up the whole app". When they point the right way, you can unit-test the business logic against plain PHP objects.

### When DRY is the wrong principle

"Don''t Repeat Yourself" is the principle most often weaponised against good code. Three real-world misapplications:

- **Two different rules that happen to look the same.** If the business logic for "VIP discount" and "weekend discount" are calculated the same way today but for unrelated reasons, *don''t* extract them. They''ll drift, and the abstraction that joined them will become tribal knowledge.
- **A shared helper that breaks one caller every time it changes.** This is the "I tried to fix the bug, broke prod for two teams" situation. The shared utility has too many opinionated callers.
- **Inheritance to remove repetition.** `class PaidOrder extends Order` looks DRY. It''s actually the worst kind of repetition because now every change to `Order` is a change to `PaidOrder` you didn''t intend.

Senior heuristic: **prefer duplication over the wrong abstraction**. Three similar functions are easier to fix than one over-general abstraction with eight optional flags.

### The "principle of least surprise"

Code is read 10x more than it''s written. Two equally-correct implementations should be judged by *which one will surprise the next reader less*.

```php
// Surprising: the cache TTL silently shadows the method param.
public function fetch(string $key, int $ttl = 60): mixed
{
    $ttl = config('cache.default_ttl', $ttl);  // ← config wins; param is ignored when set
    return Cache::remember($key, $ttl, ...);
}

// Less surprising: explicit precedence in the name.
public function fetchWithConfigTtl(string $key): mixed { /* ... */ }
public function fetchWithCustomTtl(string $key, int $ttl): mixed { /* ... */ }
```

If you read the surprising version six months from now, you''ll think the `$ttl = 30` you passed is being used. It isn''t. That''s a bug born at write-time and discovered at 2am.

## Senior insights (giving reviews + strategic)

### How to give a code review that actually helps

The hardest part of code review isn''t spotting issues — it''s communicating them so the author wants to fix them. Three habits:

- **Distinguish must-fix from nice-to-have.** Tag every comment with one of: `BLOCKING`, `SUGGESTION`, `QUESTION`, `NIT`. Reviewers who don''t tag end up with a 40-comment PR that looks impossible to address; tagged PRs get merged in a day.
- **Phrase suggestions as questions.** "Did you consider naming this `findOrFetch` so the side effect is in the name?" lands better than "Bad name." even though both want the same change.
- **Approve incrementally.** If the diff is mostly good but has one BLOCKING issue, leave the BLOCKING comment and *approve* the rest. The author feels progress; you also gave a clear bar.

### What to NOT comment on

The most senior reviewers comment on fewer things than the second-most-senior reviewers, not more. Things to skip:

- **Formatting.** That''s the linter''s job. If your project doesn''t have one, fix that, not the diff.
- **Personal style preferences.** "I would have used a match instead of switch" is noise unless there''s a reason the match is meaningfully better here.
- **Pre-existing code in the diff''s context.** Unless the PR is specifically refactoring it, don''t hijack the PR.

Every noise comment makes the next signal comment less effective. Reviewers have a budget.

### Strategic review: what won''t we notice for six months?

After you''ve covered correctness, naming, and structure, ask the long-horizon questions. These are what mid-level reviewers miss and what makes a senior''s review distinctive:

- **Is this change reversible?** A column rename can be backed out in a sprint. A column *removal* requires a multi-deploy migration. Surface the difference.
- **Did this PR shift a load to a hot path?** A new `with('relation')` looks like an N+1 fix; if the relation hydrates 50,000 rows on the index page, you just made the index page 10s slower.
- **Will this still make sense after the next feature?** If the team is about to add multi-region, the `now()` call you''re reviewing should already be `now()->in($region)`. Block the PR on the missing seam, not on its absence today.

### Interview question: *"Walk me through how you''d review this PR."*

A senior answer for a hypothetical 200-line PR:

1. **Read the description first.** What was the author trying to do? If the description doesn''t say, that''s the first comment.
2. **Skim the diff structurally.** Which files changed? A diff that touches a controller + a migration + a queue worker is doing three things, and the PR description should explain why.
3. **Read tests before code.** Tests show what the author considers important. Missing test for the new branch you spot? Note it.
4. **Read the code top-down.** Entry points first (route → controller → service), then the leaves (domain methods, queries).
5. **Run the changed file mentally with the failure case.** "What happens if the API returns 500?" "What happens if the user clicks twice?"
6. **Leave comments grouped by file**, with BLOCKING/SUGGESTION/NIT tags.
7. **Approve or request changes — never both.** Pick a state.

If the candidate skips steps 1, 2, 5 and jumps to "I look for code style", they''re an LGTM-clicker, not a reviewer.
