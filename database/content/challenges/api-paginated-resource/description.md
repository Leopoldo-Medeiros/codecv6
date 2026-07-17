Your `/jobs` endpoint returns a raw array — the front-end team needs a consistent, paginated envelope. Implement `paginate()` so every list endpoint answers with the same `{ data, meta }` shape.
