## Bug Report

`cloneCartWithNewDiscount()` is supposed to duplicate a shopping cart and
give the copy a different discount, leaving the original cart untouched.
QA found that changing the clone's discount also changes the original
cart's discount — as if they were the same object.

PHP's `clone` keyword only does a **shallow** copy: nested objects are
still shared between the original and the clone unless `__clone()` says
otherwise. The tests below are already written and one of them currently
**fails**. Find the bug and fix it.
