## The situation

Your import service normalizes vendor SKUs in four steps — lowercase, trim,
strip spaces, prefix. Today it's one deeply nested call that reads
inside-out: `prefix(strip(trim(lower($sku))))`. The next feature adds two
more steps, and the review comment writes itself: *"can this read top-to-
bottom, like the spec does?"*

Laravel answers with `Str::of($sku)->lower()->trim()…` and collections
pipelines. Underneath all of them sits one tiny functional idea: **compose a
list of functions into one function**. Building `pipe()` yourself is the
fastest way to stop treating that idea as magic.

## Your task

Implement `pipe(callable ...$fns): callable` using `array_reduce`:

- Applies the callables **left to right**: `pipe(f, g, h)($x) === h(g(f($x)))`.
- `pipe()` with no arguments returns the **identity** function:
  `pipe()($x) === $x`.
- Works with any callable — closures, `'strlen'`, first-class callables.

```php
$slugify = pipe(
    fn (string $s) => strtolower($s),
    fn (string $s) => trim($s),
    fn (string $s) => str_replace(' ', '-', $s),
);

$slugify('  Hello World  '); // 'hello-world'
```

## Examples

| **Call** | **Evaluates as** | **Result** |
| `pipe($addOne, $double)(3)` | `double(addOne(3))` | `8` |
| `pipe('strlen')('hello')` | `strlen('hello')` | `5` |
| `pipe()(42)` | identity | `42` |

## Hints

- **Hint 1:** you're reducing *functions*, not data: start from the identity (`fn ($x) => $x`) and at each step wrap: `fn ($carry, $fn) => fn ($x) => $fn($carry($x))`. Yes, a closure returning a closure — read it twice, it's the whole exercise.
- **Hint 2:** left-to-right order falls out naturally if the new function runs *after* the accumulated one — `$fn($carry($x))`, not `$carry($fn($x))`. The `addOne, double` test (3 → 4 → 8, not 3 → 6 → 7) is your order detector.
- **Hint 3:** alternative implementation for contrast: a closure with `foreach ($fns as $fn) { $x = $fn($x); }`. Both pass; the `array_reduce` version is the one that teaches composition.

## In the real world

You've built the engine behind fluent APIs: Laravel's `Pipeline` (the class
that runs every HTTP request through the middleware stack) is exactly
`pipe()` with dependency injection — middleware in, composed handler out.
`collect()->map()->filter()` chains and `Str::of()` are the same
left-to-right idea with objects. And when you meet `compose()` in a
functional library, it's your `pipe()` with the order flipped
(right-to-left). Once you can write the reducer from memory, none of those
abstractions are magic again — they're twenty lines you happen to have
already written.
