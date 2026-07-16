## Your API is Slow — Find the Bottleneck

Your product listing page takes seconds to load. You open the trace and there it is: a wall of ~60 near-identical database spans. The endpoint is querying the database **once per product** just to fetch each one's category name — the classic **N+1 query**.

**Your job:** rewrite `listProductsWithCategories()` so it hits the database a *constant* number of times (batch the category lookups with `findCategories()`) while returning the exact same result.

Done right, the trace collapses from ~60 spans to just one or two. That's the difference between an endpoint that crawls and one that flies — and you found it by **reading the telemetry**, not by guessing.
