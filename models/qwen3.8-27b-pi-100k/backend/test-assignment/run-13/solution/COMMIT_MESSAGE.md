# COMMIT_MESSAGE.md

feat: консольное приложение учёта расходов LLM-токенов

Реализовано:

**Импорт данных**
- `app:prices:import` — идемпотентный upsert провайдеров, моделей и строк цен
- `app:usage:import` — загрузка событий (JSONL) с дедупликацией по
  (provider, provider_request_id)

**Тарификация**
- `app:billing:rate` — расчёт стоимости по схеме ценообразования
  (per_1m_tokens / per_1m_tokens_unified), оф-пик, HALF_UP округление
  до 6 знаков. Идемпотентен.

**Отчёт**
- `app:billing:report` — CSV в stdout: сумма по (user, provider/model),
  сортировка, TOTAL. Поддерживает --from/--to.

**Бонус**
- `app:billing:reprice` — ретро-коррекция цены: upsert строки, пересчёт
  связанных CostRecord, вывод REPRICED_EVENTS и REPRICE_DELTA_USD.

**Архитектура**
- Domain: Money (bcmath), PriceUnit, PricingCalculator
- Infrastructure: Doctrine-сущности (Provider, Model, PriceRow,
  UsageEvent, CostRecord), кастомные репозитории
- Application: PriceImportService, UsageImportService, RateService,
  ReportService, RepriceService
- Commands: Symfony Console команды для всех операций

**Тесты** — 19 тестов, 33 ассерции (PHPUnit).
