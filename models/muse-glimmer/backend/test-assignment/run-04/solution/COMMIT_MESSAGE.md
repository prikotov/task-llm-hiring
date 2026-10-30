Implement LLM token billing backend

- Add entities Provider, Model, Price, UsageEvent with Doctrine ORM mapping
- Create migrations for schema
- Implement commands:
  app:prices:import, app:usage:import, app:billing:rate, app:billing:report
- Billing calculation uses bcmath, half-up rounding, off-peak window handling, price selection by valid_from
- Idempotent imports and duplicate event handling
