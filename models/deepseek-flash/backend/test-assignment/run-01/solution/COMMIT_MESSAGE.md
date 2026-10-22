feat: add LLM token billing console application

- import the provider/model price catalogue idempotently;
- import usage events with deduplication by (provider, provider_request_id);
- rate events using the price effective at the event time, including the UTC
  off-peak discount;
- build a CSV report grouped by user and provider/model with exact decimal
  arithmetic;
- support retroactive price corrections with recalculation and an audit
  journal;
- add a Doctrine migration, domain/parser tests and console commands.
