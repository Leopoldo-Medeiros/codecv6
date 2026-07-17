## Untangle This Git History

A merge conflict has corrupted the deployment. Two developers edited the same config file and force-pushed. You need to reconstruct the correct state from the commit messages.

**Goal:** Implement a `mergeConfigs()` function that takes two conflicting config arrays and a resolution strategy.

Strategies: `"ours"`, `"theirs"`, `"combine"`
