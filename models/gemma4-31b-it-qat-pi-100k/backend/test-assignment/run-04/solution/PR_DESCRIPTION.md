# PR Description: LLM Token Cost Accounting

## What was done
Implemented a console application for tracking costs of LLM token usage. The system imports price lists and usage events, calculates costs based on available prices at the time of event, and generates a summary report.

### Key features:
- **Precision Arithmetic**: Used `bcmath` for all financial calculations to avoid floating-point inaccuracies.
- **Rounding**: Implemented `HALF_UP` rounding to 6 decimal places for each individual usage event.
- **Off-peak Pricing**: Supported UTC time windows for cost multipliers.
- **Price Versioning**: Events are charged using the price that was active at the event's timestamp.
- **Deduplication**: Usage events are deduplicated by `(provider, provider_request_id)`.
- **Graceful Handling of Unrated Events**: Events without a valid price are marked as unrated and excluded from the report, with an option to re-process them if prices are imported later.

## Implementation Details
- **Database**: PostgreSQL 17 with Doctrine ORM.
- **Entities**: 
    - `Provider`: Stores provider details and off-peak settings.
    - `Model`: Links providers to specific LLM models.
    - `Price`: Stores time-versioned pricing rates for models.
    - `Usage`: Stores usage events and their calculated costs.
- **Commands**:
    - `app:prices:import`: Idempotent import of pricing data.
    - `app:usage:import`: Idempotent import of usage events.
    - `app:billing:rate`: Processes all currently unrated events.
    - `app:billing:report`: Generates a CSV report of costs grouped by user and model.

## Assumptions and Decisions
- **Rounding edge cases**: Rounding is performed at the event level, as specified. The `roundHalfUp` implementation uses string manipulation of `bcmath` results to ensure precision.
- **Model mapping**: The `Usage` entity stores both a reference to the `Model` entity and a `modelCode` string to allow importing usage events even if the model is not yet defined in the system.
- **Reserved Keywords**: Changed column name `user` to `userName` in `Usage` entity because `user` is a reserved keyword in PostgreSQL.

## Verification Steps
1. `make db-up`
2. `bin/console doctrine:schema:update --force`
3. `bin/console app:prices:import fixtures/prices.json`
4. `bin/console app:usage:import fixtures/usage.jsonl`
5. `bin/console app:billing:rate`
6. `bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z`
7. Compare output with the example in `TASK.md`.
