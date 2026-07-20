## The situation

`RequestIdGenerator` hands out sequential IDs starting from 1. Simple —
until a teammate's integration test created a **second, brand-new generator**
and it started counting at 5, as if it remembered the first generator's
history.

New object, inherited state. That's not a race condition and not test
pollution from a database — the generator itself is carrying memory it
shouldn't have.

## Your task

The tests are already written and one currently **fails**:

| **Scenario** | **Expected** |
| first instance, two calls | `1`, then `2` |
| a *new* instance after the first was used | starts over at `1` |

Fix the class so each instance counts independently.

## Hints

- **Hint 1:** read the property declaration character by character. One keyword changes *where* that counter lives — on the class (shared by every instance) or on the object.
- **Hint 2:** `self::$counter` and `$this->counter` are not two syntaxes for the same thing — they address two different storage locations.

## In the real world

`static` state is shared across every instance *and every request that reuses
the process*. In classic PHP-FPM each request gets a fresh process, which
hides bugs like this; under Laravel Octane, Swoole, or a long-running queue
worker, the process survives — and static counters, caches and singletons leak
from one request into the next. This exact class of bug is the #1 gotcha in
every "moving to Octane" checklist. When you *want* shared sequence state, it
belongs in something built for it: a database sequence, Redis `INCR` — not a
static property.
