Implemented an LLM token billing system.

Key features:
- High-precision decimal arithmetic using bcmath for costs.
- Support for different pricing models (per-token and unified).
- Time-weighted pricing based on the event timestamp.
- Off-peak discount windows with midnight-crossing support.
- Idempotent imports for prices and usage events.
- Billing report generation with aggregation by user and model.
- Bonus: Retrospective price correction (reprice) functionality.

Detailed implementation:
- Domain entities for Provider, Model, Price, and UsageEvent.
- Dedicated BillingRateCalculator service for pricing logic to ensure consistency.
- Console commands for data import, rating, and reporting.
- Database schema managed via Doctrine ORM.
