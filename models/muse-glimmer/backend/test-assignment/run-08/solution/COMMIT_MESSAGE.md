feat(billing): implement LLM token cost accounting

- Add migrations for providers, models, prices, usage_events
- Implement app:prices:import with idempotent upsert for providers/models/prices
- Implement app:usage:import with idempotent upsert by (provider, provider_request_id)
- Implement app:billing:rate with price selection by valid_from, off-peak multiplier, decimal arithmetic via BCMath, half-up rounding to 6 decimals, unrated handling
- Implement app:billing:report with CSV output, grouping by user/model, totals
- Use Doctrine DBAL, Symfony Console, PostgreSQL
