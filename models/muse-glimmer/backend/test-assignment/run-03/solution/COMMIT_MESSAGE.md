feat(billing): implement LLM token billing with prices import, usage import, rating and reporting

- Add Doctrine entities: Provider, Model, Price, UsageEvent
- Implement commands:
  * app:prices:import — idempotent upsert providers/models/prices
  * app:usage:import — idempotent usage JSONL import with deduplication
  * app:billing:rate — price selection by valid_from, off-peak multiplier, bcmath
    calculations with HALF_UP rounding to 6 decimals, unrated handling
  * app:billing:report — CSV aggregation by user/model with totals
- Migrations created for schema
- All operations use decimal arithmetic, no float for money
