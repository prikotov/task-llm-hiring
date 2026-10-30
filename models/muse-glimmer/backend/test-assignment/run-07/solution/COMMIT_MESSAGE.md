Implement LLM token billing domain with Symfony commands

- Add Doctrine entities: Provider, Model, Price, UsageEvent
- Migrate schema via Doctrine migrations
- Implement app:prices:import (idempotent upsert providers/models/prices)
- Implement app:usage:import (JSONL, dedupe by provider+request_id)
- Implement app:billing:rate (price selection, per-token/unified tariffs, off-peak multiplier, bcmath rounding HALF_UP, unrated handling)
- Implement app:billing:report (CSV aggregation by user/model with date filter)
- Use decimal arithmetic, avoid floats, respect off-peak window crossing midnight
