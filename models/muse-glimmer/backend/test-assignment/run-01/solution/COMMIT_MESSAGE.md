feat(billing): implement LLM token cost accounting

- Add Doctrine entities: Provider, Model, Price, UsageEvent
- Initial migration for schema
- Commands:
  * app:prices:import - upsert providers/models/prices
  * app:usage:import - idempotent JSONL import with deduplication
  * app:billing:rate - rate unrated events with price lookup, off-peak, BCMath rounding
  * app:billing:report - CSV report grouped by user/model with totals
- Use BCMath for monetary calculations, HALF_UP rounding to 6 decimals
- Off-peak window handling with midnight crossing
- Idempotent imports and rating
