Implemented a billing system for LLM token usage.

- Added entities for Provider, Model, Price, and UsageEvent.
- Implemented `app:prices:import` to idempotently import provider, model, and price data.
- Implemented `app:usage:import` to idempotently import usage events.
- Implemented `app:billing:rate` to calculate costs for unrated events using decimal arithmetic (`bcmath`) and handling off-peak discounts.
- Implemented `app:billing:report` to generate a CSV report of costs per user and model.
- Handled potential reserved keyword conflicts (e.g., `user` column) by mapping to `username` in the database.
- Ensured precision and rounding requirements are met.
