feat: implement LLM token billing with price import, usage import, rating and reporting

- Add Doctrine entities: Provider, Model, Price, UsageEvent
- Add migration for initial schema
- Implement commands:
  * app:prices:import – idempotent upsert of providers/models/prices
  * app:usage:import – idempotent upsert of usage events by (provider, provider_request_id)
  * app:billing:rate – rate unrated events using best valid_from price, off-peak multiplier, decimal arithmetic with bc math, rounding HALF_UP to 6 decimals; outputs RATED/UNRATED summary
  * app:billing:report – CSV report grouped by user/model with token sums and cost sums
- Cost calculation handles per_1m_tokens and per_1m_tokens_unified schemes, off-peak window crossing midnight, precise decimal arithmetic
- Schema created via Doctrine Migrations
