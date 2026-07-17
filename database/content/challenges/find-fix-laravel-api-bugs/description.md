## Find and Fix the Bugs in This Laravel API

An e-commerce API is in production with critical bugs reported by users. You have access to the code and the logs. No stack trace provided — you need to find, reproduce and fix each problem.

### Bug #1: "Orders appear duplicated at checkout"

When a user submits an order, it sometimes appears twice in the order history. The checkout endpoint has a race condition.

### Bug #2: "Admin can delete their own account"

The admin endpoint doesn't check if the user is trying to delete their own account. This is a critical authorization bug.

### Bug #3: "API returns 500 when product is out of stock"

When a product is out of stock, instead of returning a proper 422 validation error, the API crashes with a 500 Internal Server Error.

**Goal:** Fix all three bugs. The tests will verify each fix.
