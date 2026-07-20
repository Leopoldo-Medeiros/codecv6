## The situation

The cart API lets a customer set an item's quantity — and setting it to `0`
is the legitimate way to remove the item. QA found that sending
`quantity: 0` gets rejected with *"Quantity is required"*, as if the field
were never submitted at all.

The validator can't tell the difference between **absent** and **falsy**.
In PHP, that distinction is a minefield with a famous name: `empty()`.

## Your task

The tests are already written and one currently **fails**:

| **Input** | **Expected** |
| `[]` | 1 error — quantity genuinely missing |
| `['quantity' => '0']` | valid — present, value is zero |
| `['quantity' => '5']` | valid |

Fix `validateQuantity()` so only true absence produces the error.

## Hints

- **Hint 1:** `empty('0')` is `true`. So is `empty(0)`, `empty('')`, `empty(null)`, `empty([])` and `empty(false)`. The function answers "is this falsy?", not "was this provided?".
- **Hint 2:** the question you actually want to ask is `array_key_exists('quantity', $input)` — presence, independent of value. (`isset` is close but treats an explicit `null` as absent; know which one you're choosing.)

## In the real world

This exact bug ships constantly in checkout flows, settings forms
("notifications: off" = `false` = "not answered"?), and rating inputs where
`0` is a legal score. Laravel encodes the distinction into separate rules:
`required` rejects `'0'`-style empties, while `present` and `filled` split
"key must exist" from "value must be non-empty" — choosing between them *is*
this kata. This project's own quiz endpoint validates its `answers` map with
`present|array` for precisely this reason: an empty answer map must reach the
grader, not bounce at validation.
