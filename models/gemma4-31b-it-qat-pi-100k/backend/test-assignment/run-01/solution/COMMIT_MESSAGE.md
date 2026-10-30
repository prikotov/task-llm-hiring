Implement LLM token billing system

- Designed and implemented database schema with Doctrine ORM: Providers, Models, Prices, and UsageEvents.
- Implemented `app:prices:import` to load pricing history from JSON.
- Implemented `app:usage:import` to import token usage events with deduplication (provider, provider_request_id).
- Implemented `app:billing:rate` to calculate costs for unrated events using the correct price for the given timestamp, including off-peak discounts.
- Implemented `app:billing:report` to generate a CSV report of token usage and costs per user and model.
- Used bcmath for high-precision decimal arithmetic to avoid float inaccuracies.
- Implemented `app:billing:reprice` (bonus) to correct pricing and update affected events with a delta report.
- Ensured idempotency for imports and rating.
- Validated the system against provided fixtures.