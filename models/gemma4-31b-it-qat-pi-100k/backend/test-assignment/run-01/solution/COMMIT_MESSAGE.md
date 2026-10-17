Implement LLM token usage billing system.

- Implemented price and usage import commands with idempotency.
- Implemented rating logic with support for per-token and unified pricing models.
- Added off-peak discount logic based on UTC time windows.
- Implemented CSV billing report with user/model grouping and date filtering.
- Used bcmath for precise decimal arithmetic and custom HALF_UP rounding to 6 decimal places.
- Implemented database schema using Doctrine ORM and migrations.
- Added bonus feature: price correction (reprice) command.
