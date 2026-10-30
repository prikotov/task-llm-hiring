# PR Description: LLM Token Billing System

## Implementation Details

I have implemented a billing system for LLM tokens that handles complex pricing rules, including time-based rates and off-peak discounts.

### Key Technical Decisions

1.  **Precision Arithmetic**: Used `bcmath` for all financial calculations to avoid floating-point inaccuracies. The scale was set to 16 for intermediate calculations, and the final event cost was rounded to 6 decimal places using the HALF_UP rule as required.
2.  **Data Model**:
    *   `Provider`: Stores provider info and off-peak windows.
    *   `Model`: Links models to providers.
    *   `Price`: Stores time-valid rates for each model.
    *   `UsageEvent`: Stores actual usage. I renamed the `user` column to `username` because `user` is a reserved keyword in PostgreSQL.
3.  **Pricing Logic**:
    *   The system finds the most recent price valid at the time of the event.
    *   It calculates cost based on the specified unit (`per_1m_tokens` or `per_1m_tokens_unified`).
    *   Applies the provider's off-peak multiplier if the event timestamp falls within the specified UTC window (including windows that cross midnight).
4.  **Idempotency**:
    *   `app:prices:import`: Uses upsert logic.
    *   `app:usage:import`: Checks for the existence of the `(provider, provider_request_id)` pair before inserting.
5.  **Performance**:
    *   Used Doctrine ORM for database interactions.
    *   Billing rating is done in a single batch per run for unrated events.

### Bonus: Repricing

I implemented the `app:billing:reprice` command. It updates existing price records and recalculates costs for all rated events that were previously billed using those specific price records, providing the total number of affected events and the financial delta.

### Verification Steps

1.  **Startup**: `make db-up` and `bin/console doctrine:schema:update --force`.
2.  **Import and Rate**:
    `bin/console app:prices:import fixtures/prices.json`
    `bin/console app:usage:import fixtures/usage.jsonl`
    `bin/console app:billing:rate`
3.  **Report**:
    `bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z`
    The output matches the example in `TASK.md` byte-for-byte.
4.  **Idempotency**: Repeated the steps above and confirmed that results remain identical.

### Deviations from Framework

*   Renamed `user` field to `username` in `UsageEvent` entity to avoid PostgreSQL reserved keyword error.
*   Used `#[AsCommand]` attribute for command naming as `static $defaultName` is deprecated/removed in newer Symfony versions.
*   Used `doctrine:schema:update --force` instead of migrations for simplified deployment in this test task.
