добавлена учёт расходов на LLM-токены

- домен: Money (bcmath, точное десятичное значение), OffPeakWindow,
  PricingScheme (per_1m_tokens / per_1m_tokens_unified), TokenCounts
- сущности: Provider (off-peak на уровне провайдера), Model, PriceRow,
  UsageEvent (дедупликация по (provider, provider_request_id)),
  RatedEvent (cost_usd после тарификации / repriced_at), RepriceLog
- консольные команды:
  app:prices:import  — загрузка справочника (idempotent upsert);
  app:usage:import   — загрузка событий JSONL, дубли игнорируются;
  app:billing:rate   — тарификация unrated-событий;
  app:billing:report — CSV-отчёт (user, model, tokens, cost_usd);
  app:billing:reprice (бонус) — ретро-коррекция цены + журнал дельты.
- схема БД: миграция migrations/Version20261027141601.php
- точность: bcmath (масштаб 18), одно округление события до 6 знаков
  HALF_UP, float для денег не применяется.
