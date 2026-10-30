Implement LLM token billing domain with Symfony commands

- Add Doctrine entities: Provider, Model, Price, UsageEvent
- Implement idempotent imports for prices and usage
- Implement billing rate with price selection, per-token and unified schemes,
  off-peak multiplier, bcmath decimal arithmetic with HALF_UP rounding
- Implement CSV report with aggregation and totals
- Add migrations and schema setup
