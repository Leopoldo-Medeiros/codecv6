## Bug Report

`RequestIdGenerator` hands out sequential IDs starting from 1 for each
new generator instance. In an integration test, a teammate created a
second, completely independent generator — and it started at 5 instead
of 1, as if it remembered the first generator's history.

The tests below are already written and one of them currently **fails**.
Find the bug and fix it.
