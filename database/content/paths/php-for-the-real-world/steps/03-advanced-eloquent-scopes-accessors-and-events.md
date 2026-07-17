---json
{
    "order": 3,
    "title": "Advanced Eloquent: Scopes, Accessors and Events",
    "type": "reading",
    "description": "Eloquent is powerful but easily misused. We cover query scopes for business rules, accessors/casts for shape transformation, model events and observers, and — most importantly — when to stop using Eloquent and write raw SQL.",
    "tldr": "Eloquent gives you four levers — scopes (reusable WHERE clauses), accessors/casts (column ↔ PHP type), observers (lifecycle hooks), and `whereRaw` (the escape hatch). Knowing which lever to pull for which problem is what separates a senior Laravel dev from someone who only writes `Model::all()->filter(...)`.",
    "difficulty": "intermediate",
    "estimated_minutes": 25,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": true,
    "playground_starter_code": "<?php\ndeclare(strict_types=1);\n\n// Sketch of an Order model showing accessor, cast, scope and observer\n// patterns side by side. In a real Laravel app each piece lives in its\n// own file/class; here they're inlined so you can see the relationships.\n\nclass OrderStatus\n{\n    public const Pending  = 'pending';\n    public const Paid     = 'paid';\n    public const Refunded = 'refunded';\n}\n\n// In a real Laravel app: app/Models/Order.php\n// class Order extends Model\n// {\n//     // Cast: column → typed property automatically\n//     protected $casts = [\n//         'status'   => OrderStatus::class,  // string-backed enum\n//         'metadata' => 'array',             // JSON column → PHP array\n//         'paid_at'  => 'immutable_datetime',\n//     ];\n//\n//     // Accessor: derived property without a column\n//     public function getTotalEurosAttribute(): float\n//     {\n//         return $this->total_cents / 100;\n//     }\n//\n//     // Local scope: reusable WHERE clause\n//     public function scopeFinal(Builder $q): void\n//     {\n//         $q->whereIn('status', [OrderStatus::Paid, OrderStatus::Refunded]);\n//     }\n// }\n//\n// Usage from a controller:\n// Order::final()->where('paid_at', '>', now()->subDays(7))->get();\n\necho \"Patterns compile OK\\n\";",
    "challenge_prompt": null,
    "resources": [
        {
            "url": "https://laravel.com/docs/eloquent#query-scopes",
            "label": "Eloquent Scopes"
        },
        {
            "url": "https://laravel.com/docs/eloquent-mutators",
            "label": "Eloquent Mutators & Casting"
        },
        {
            "url": "https://laravel.com/docs/eloquent#events",
            "label": "Model Events & Observers"
        },
        {
            "url": "https://laravel.com/docs/eloquent#chunking-results",
            "label": "Eloquent chunk + lazy"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Extract a scope — find a controller where the same Eloquent where() is repeated across two or more actions. Move it to a scope on the model and refactor the call sites. Confirm `Model::yourScope()` produces the same SQL via toSql().",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\n// Take this repetitive controller code and refactor `where('status', 'active')->where('paid_at', '!=', null)`\n// into a single scope `active()` on the model.\n\nclass OrderController\n{\n    public function index()\n    {\n        return Order::where('status', 'active')->where('paid_at', '!=', null)->paginate(20);\n    }\n\n    public function export()\n    {\n        return Order::where('status', 'active')->where('paid_at', '!=', null)->get()->toCsv();\n    }\n}\n\n// TODO: on the Order model, define scopeActive(Builder $query): void\n// TODO: rewrite both controller actions to call Order::active() instead."
        },
        {
            "id": 2,
            "text": "Spot the N+1 — given the snippet below, identify how many DB queries it issues. Fix it with the right with() call so it issues exactly two queries regardless of the number of orders.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\n// N+1 bug: one query for orders + one per order for customer.name.\n// 100 orders → 101 queries. Add a `with()` to fix it.\n\n$orders = Order::where('status', 'paid')->get();\n\nforeach ($orders as $order) {\n    echo $order->customer->name . ' bought ' . $order->total_cents . PHP_EOL;\n}\n\n// TODO: rewrite the first line so the loop above runs in 2 queries total."
        },
        {
            "id": 3,
            "text": "Build a backed-enum cast — define an OrderStatus enum (pending/paid/refunded) and cast it on the Order model. Then prove with a tinker session that $order->status === OrderStatus::Paid works (not a string compare).",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\nenum OrderStatus: string\n{\n    case Pending  = 'pending';\n    case Paid     = 'paid';\n    case Refunded = 'refunded';\n}\n\n// In a real Order model you would add:\n// protected $casts = [\n//     'status' => OrderStatus::class,\n// ];\n//\n// Then this should evaluate to true (using === — not ==):\n\n$status = OrderStatus::Paid;\nvar_dump($status === OrderStatus::Paid);            // true\nvar_dump($status === 'paid');                        // false — different types\nvar_dump(OrderStatus::tryFrom('paid') === $status);  // true"
        },
        {
            "id": 4,
            "text": "Write an Observer — implement OrderObserver with a `creating` hook that fills `reference` with a ULID when null, and an `updated` hook that fires an OrderPaid event when status changes to Paid. Register it in a Service Provider's boot().",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\nnamespace App\\Observers;\n\nclass OrderObserver\n{\n    public function creating(/* Order $order */): void\n    {\n        // TODO: if $order->reference is null, assign a ULID\n        // ($order->reference ??= Str::ulid())\n    }\n\n    public function updated(/* Order $order */): void\n    {\n        // TODO: if status was just changed to \"paid\", fire an OrderPaid event\n        // Hint: $order->wasChanged('status') tells you if the column changed in this save.\n    }\n}\n\n// In your AppServiceProvider::boot():\n// Order::observe(OrderObserver::class);"
        },
        {
            "id": 5,
            "text": "Senior interview drill — given a report query that joins 6 tables and aggregates 12 metrics over 2 years of data, decide: do you write it in Eloquent, raw SQL, or a database view? Justify the choice in 2 sentences. (Hint: there's a 'right' answer that interviewers listen for — and it's not Eloquent.)",
            "starter_code": null
        }
    ],
    "prerequisites": [
        {
            "id": 1,
            "title": "Modern PHP: Types, Nullables and Enums"
        },
        {
            "id": 2,
            "title": "Laravel Request Lifecycle"
        }
    ],
    "concepts": [
        "query-scopes",
        "accessors",
        "mutators",
        "casts",
        "observers",
        "model-events",
        "raw-sql",
        "n+1"
    ],
    "quiz": null,
    "evidence": null
}
---
## Core (foundations)

### Query scopes: reusable WHERE clauses

A *local scope* gives a piece of query logic a name. Instead of repeating `where('status', 'paid')` in five controllers, you write it once on the model:

```php
class Order extends Model
{
    public function scopePaid(Builder $query): void
    {
        $query->where('status', 'paid');
    }

    public function scopeForCustomer(Builder $query, int $customerId): void
    {
        $query->where('customer_id', $customerId);
    }
}

// At the call site, the `scope` prefix is dropped:
Order::paid()->forCustomer(42)->get();
```

Two real wins:
- **Refactor-safe.** Rename the `status` column once and every scope-using query updates.
- **Composable.** Chain scopes the same way you'd chain query-builder methods.

Global scopes apply to *every* query on the model (`Builder::addGlobalScope`). Use them for soft-deletes, multi-tenancy, or any "always filter by tenant_id" rule. Strip them per-query with `withoutGlobalScope(...)` when you need to.

### Accessors and casts

A **cast** turns a database column into a richer PHP type *automatically*. You declare it once on the model and every read/write goes through the conversion:

```php
class Order extends Model
{
    protected $casts = [
        'status'     => OrderStatus::class,        // backed enum
        'metadata'   => 'array',                   // JSON ↔ PHP array
        'paid_at'    => 'immutable_datetime',      // DATETIME ↔ CarbonImmutable
        'total_cents'=> 'integer',
    ];
}

$order = Order::find(1);
$order->status;          // OrderStatus::Paid (not a string)
$order->metadata['ip'];  // works — already decoded
```

An **accessor** is a *derived* property that doesn't live in the database — it's computed every time you read it:

```php
class Order extends Model
{
    protected function totalEuros(): Attribute
    {
        return Attribute::get(fn () => $this->total_cents / 100);
    }
}

$order->total_euros;  // float, calculated on the fly
```

Casts for storage shape. Accessors for derived presentation. Don't confuse the two — accessors in JSON responses are great, but accessing them in a `WHERE` clause won't work (they aren't columns).

### Eager loading and N+1

This is the most common Eloquent bug, and it shows up in every code review:

```php
// ❌ N+1: one query for users, then one per user for posts (101 queries for 100 users).
$users = User::all();
foreach ($users as $user) {
    echo $user->name . ': ' . $user->posts->count();
}

// ✅ Eager loaded: 2 queries total.
$users = User::with('posts')->get();
```

`with('relation')` joins-or-batches the related rows in *one* extra query. Use it any time you're going to access a relation in a loop. The N+1 problem is what `withCount()`, `whereHas()`, and `loadMissing()` exist to prevent — learn all three.

## Deeper dive (intermediate Eloquent)

### Model events and observers

Every model emits lifecycle events: `creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`, `retrieved`. You can hook into them with closures or, more sustainably, with an **Observer** class:

```php
class OrderObserver
{
    public function creating(Order $order): void
    {
        $order->reference ??= Str::ulid();
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->status === OrderStatus::Paid) {
            event(new OrderPaid($order));
        }
    }
}

// Register in a Service Provider's boot():
Order::observe(OrderObserver::class);
```

Why observers beat scattered model logic:
- **One place** for "what happens when an order is created". A new dev grep's the observer; they don't trawl through six places that all call `Order::create()`.
- **Mass operations bypass observers** (`Order::query()->update()`, `Order::query()->delete()`). That's both a feature (skip the side effects when you want a fast bulk update) and a footgun (forget that, ship a bug). When you intentionally bypass, drop a comment explaining why.

### Custom Casts: when built-ins aren't enough

Sometimes a column needs a non-standard PHP shape. Built-in casts cover scalars, JSON, dates, enums. For everything else, write a **custom cast**:

```php
class EuroMoneyCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): Money
    {
        return new Money((int) $value, 'EUR');
    }

    public function set($model, $key, $value, $attributes): int
    {
        return $value instanceof Money ? $value->cents : (int) $value;
    }
}

// On the model:
protected $casts = ['total' => EuroMoneyCast::class];
```

The two-way conversion happens *every time* you touch the attribute. Use this for value objects — `Money`, `Coordinate`, `Range` — anything that's "a thing", not just a primitive.

### When to drop down to raw SQL

Eloquent is a sharp tool for CRUD. It's a terrible tool for:

- **Bulk operations.** `Order::query()->where(...)->update(...)` is fine. `$orders->each(fn ($o) => $o->update(...))` runs 1000 separate queries.
- **Window functions, CTEs, recursive queries.** The query builder doesn't speak `WITH RECURSIVE`. Use `DB::statement()` or write a stored procedure.
- **Analytics queries.** A report that joins 5 tables, groups by 3 dimensions and aggregates 10 metrics: do it in raw SQL or a database view. Eloquent will hydrate millions of Model objects you don't need.

Use `DB::raw()` for column expressions inside a builder:

```php
Order::select('region', DB::raw('SUM(total_cents) as total'))
    ->groupBy('region')
    ->get();
```

And `DB::select(...)` for an entirely hand-written query. Knowing when *not* to use Eloquent is the seniorest thing you can know about Eloquent.

## Senior insights (architecture & code review)

### Code-review patterns to call out

Things a senior would flag on a PR involving Eloquent:

- **`->get()` without `->select(['col1', 'col2'])`.** A `Model::all()` reads every column of every row. On a wide table with TEXT/JSON columns, that's hundreds of kB per row that never reach the response.
- **`$model->relation->...` in a Blade loop without `@foreach ($models->with('relation'))` upstream.** Classic N+1 hidden behind a template.
- **Mass assignment of `$request->all()`.** Always pass a *validated* array. `$model->fill($request->validated())` is the senior pattern.
- **`Model::find($id)` instead of `findOrFail($id)`.** The difference between a 404 and a NullPointer-style crash three layers deep.
- **Querying inside accessors.** `getNameAttribute()` that does `Country::find($this->country_id)->name` runs a query on every property access. It's silent N+1 on steroids.

### Performance: the `chunk` family

A standard `Order::all()->each(...)` loads every row into memory at once. For tables with millions of rows, that OOMs your worker. Use `chunk`:

```php
Order::query()->chunk(500, function ($orders) {
    foreach ($orders as $order) { /* process */ }
});
```

`chunk` pages through the result set in 500-row batches. `chunkById()` is safer if the table is mutating while you read (uses `id > last_id` instead of `OFFSET`). `lazy()` and `lazyById()` give you a `Generator` for the same pattern with cleaner syntax.

### Multi-tenancy gotchas

Tenant isolation usually rides on a global scope:

```php
static::addGlobalScope('tenant', fn ($q) => $q->where('tenant_id', auth()->user()->tenant_id));
```

What goes wrong:

- **Background jobs.** The auth guard isn't bound in queue workers — `auth()->user()` is null and your scope filters everything out. Workers need an explicit `actAs($tenant)` helper that binds tenant context.
- **Migration commands.** Same problem. Run with `--no-interaction` or scope-aware tenant commands.
- **`withoutGlobalScope`.** Calling it once "to do something quick in admin" disables tenant isolation. Make those calls grep-able (`withoutTenantScope` wrapper) so security review can audit them.

### Interview question: *"How would you optimise an Eloquent query that's slow?"*

A senior answer walks the staircase, not the elevator:

1. **EXPLAIN the query.** Look at the index usage. Half the time the column you're filtering on doesn't have an index.
2. **Look for N+1.** Run with the Laravel debug bar (or Telescope). One query in the controller, fifty in the Blade template means you missed `with()`.
3. **Profile column count.** Is `SELECT *` reading TEXT/JSON columns you don't need? Add an explicit `select()`.
4. **Reconsider Eloquent itself.** If you're aggregating across half a million rows, raw SQL or a database view is the answer. Hydration cost is real.
5. **Cache the result.** If the query is read-heavy and rarely changes, `Cache::remember(...)` it. But only after you've fixed the underlying query — caching a slow query just spreads the cost across the cache TTL.

If the candidate jumps straight to "I'd add Redis", they're optimising the wrong layer.
