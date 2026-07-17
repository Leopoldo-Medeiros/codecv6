## Bug Report

`validateQuantity()` should reject a request only when `quantity` is
completely **missing**. A customer ordering `0` units (to remove an item,
say) is a valid, present value — but the validator rejects it as if it
were never submitted at all.

The tests below are already written and one of them currently **fails**.
Find the bug and fix it.
