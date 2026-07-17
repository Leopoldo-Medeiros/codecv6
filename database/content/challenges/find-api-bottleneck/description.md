## Your App is Slow — Find the Bottleneck in 30 Minutes

Users report that the product listing page takes 8+ seconds to load. Profile the code and identify the N+1 query problem, then fix it.

**Goal:** Rewrite `getProductsWithCategories()` to batch the category lookups instead of querying per product.
