Учёт расходов на LLM-токены: биллинг-модуль с точной арифметикой и ретро-коррекцией

Реализован консольный биллинг-модуль, принимающий историю цен (JSON) и
события использования (JSONL), тарифицирующий события по цене, действовавшей
в момент запроса, и печатающий CSV-отчёт расходов по парам (user, model).

- Домен `App\Domain\Billing`: сущности Provider / Model / PriceRow / UsageEvent,
  enum PriceUnit, Money (bcmath, без float), BillingCalculator, OffPeak,
  BillingException.
- Прикладной слой `App\Application\Billing`: PriceImporter (идемпотентный
  upsert справочника), UsageImporter (дедупликация по (provider,
  provider_request_id)), RatingService (попытка rate для всех unrated при
  каждом запуске), ReportService (CSV со строкой TOTAL), RepriceService
  (ретро-коррекция цены с пересчётом затронутых событий).
- Команды `bin/console`: app:prices:import, app:usage:import,
  app:billing:rate, app:billing:report [--from=--to=], app:billing:reprice.
  CSV и сводка — строго в stdout, служебные сообщения (UNRATED-строки) — в
  stderr.
- Миграция схемы (Doctrine) на чистой базе: `make db-up && make migrate`.
- Проверено на примерах из fixtures/prices.json и fixtures/usage.jsonl:
  отчёт сентября байт-в-байт совпадает с примером из TASK.md; повторный
  импорт + rate + report — результат неизменен (идемпотентность); unrated-
  события не попадают в отчёт и повторно тарифицируются при каждом rate;
  reprice применяет исправленную строку цен и выводит
  REPRICED_EVENTS / REPRICE_DELTA_USD.
