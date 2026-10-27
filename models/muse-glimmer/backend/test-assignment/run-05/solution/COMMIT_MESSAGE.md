Implement LLM token billing with Symfony/Doctrine

- Added entities Provider, Model, Price, UsageEvent
- Commands: app:prices:import, app:usage:import, app:billing:rate, app:billing:report
- Decimal arithmetic with bcmath, HALF_UP rounding, off-peak window, price selection, dedup
- Schema via doctrine:schema:update
