Implement LLM token billing with Symfony/Doctrine

- Add entities Provider, Model, Price, UsageEvent with composite keys
- Migrations for schema
- Commands: app:prices:import, app:usage:import, app:billing:rate, app:billing:report
- Decimal arithmetic via bcmath, HALF_UP rounding, off-peak handling, price selection by valid_from, deduplication
