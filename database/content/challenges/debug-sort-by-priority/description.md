## The situation

The support dashboard should list tickets `high` first, then `medium`, then
`low`. It compiles, it sorts, nothing crashes — but agents see `high`, `low`,
`medium` and keep missing mid-priority tickets buried at the bottom.

Look at that wrong order again: `high`, `low`, `medium`. It's not random.
It's **alphabetical** — the comparator is sorting the *words*, not the
*priorities*.

## Your task

The test is already written and currently **fails**:

```php
$tickets = [low 'A', high 'B', medium 'C'];
$this->assertSame(['B', 'C', 'A'], array_column(sortByPriority($tickets), 'title'));
```

Fix `sortByPriority()` so domain order (`high` → `medium` → `low`) wins over
string order.

## Hints

- **Hint 1:** the spaceship operator does exactly what it's told: `'high' <=> 'low'` compares strings. Give it numbers instead — map each priority to a rank (`['high' => 0, 'medium' => 1, 'low' => 2]`) and compare the ranks.
- **Hint 2:** define the rank map *once* outside the comparator closure; a lookup table inside `usort`'s callback is rebuilt on every comparison.

## In the real world

"Sorts fine, wrong order" is the signature of sorting by representation
instead of meaning — the same bug family as `"10" < "9"` in string-compared
version numbers and dates sorted as `dd/mm/yyyy` text. The durable fix is to
make the domain order *first-class*: a backed enum with a `rank()` method
(this project's `ChallengeDifficulty` enum does exactly this), or `ORDER BY
FIELD(priority, 'high','medium','low')` when the database sorts. If the order
lives in one authoritative place, the dashboard and the SQL can't disagree.
