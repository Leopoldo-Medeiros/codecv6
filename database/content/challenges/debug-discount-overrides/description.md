## Bug Report

`applyDiscountOverrides()` takes a base list of per-product discounts
(keyed by product ID) and a smaller list of overrides, and is supposed to
return the base list with the overrides applied on top — same product ID,
new discount value. In production, product 101's override is completely
missing from the result, and the product IDs used to look up discounts
elsewhere in the code stop working entirely.

The test below is already written and currently **fails**. Find the bug
and fix it — the fix is choosing a different built-in function.
