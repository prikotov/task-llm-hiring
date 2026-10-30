feat: LLM-токены — учёт расходов (полный срез)

Реализовано:
  - Doctrine-схема: providers (c off-peak), models, price_rows, usage_events
  - app:prices:import  — идемпотентный upsert по code / (provider, code) /
                         (provider, model, valid_from)
  - app:usage:import   — дедупликация по (provider, provider_request_id)
  - app:billing:rate   — per-token И unified-схемы, оф-пик, unrated;
                         точная арифметика bcmath (float запрещён),
                         ровно 1 округление HALF_UP до 6 знаков
  - app:billing:report — CSV по (user, model), периоды --from/--to
  - app:billing:reprice (бонус) — ретро-коррекция строк цен,
                         REPRICED_EVENTS / REPRICE_DELTA_USD

Архитектура:
  - App\Domain  — Money, Provider, Model, PriceRow, UsageEvent,
                  EventCostCalculator (чистая бизнес-логика, без БД)
  - App\Repository\DomainRepository — единая точка доступа к Doctrine ORM
  - App\Application\Prices|Billing|Usage|Reports — use-cases
  - App\Command — консолидные обёртки (stdout vs stderr)

Фикстуры воспроизводят пример из TASK.md байт-в-байт.
