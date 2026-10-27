# COMMIT_MESSAGE

## Учёт расходов на LLM-токены: CLI-приложение на Symfony + Doctrine + PostgreSQL

- **Домен** (`src/Domain/`): неизменяемые value objects — `Money` (bcmath,
  HALF_UP до 6 знаков), `PricingScheme` (пер_1m_tokens / per_1m_tokens_unified),
  `OffPeakWindow` (окно UTC, пересекающее полночь), `PricedModel` (ставки +
  схема), `TokenBreakdown`, `RateCalculator` (точная цена → единственное
  округление).
- **Приложение** (`src/Application/`): `PricesImportService` (upsert по
  natural keys провайдер/модель/`valid_from`), `UsageImportService` (дедупликация
  по `(provider, provider_request_id)`, повторная доставка игнорируется),
  `RateService` (тарификация unrated событий с повторными попытками),
  `ReportService` (CSV-отчёт, группировка по `(user, model)`, `TOTAL`),
  `RepriceService` (бонус: ретро-коррекция цены с дельтой).
- **Команды** (`src/Command/`): `app:prices:import`, `app:usage:import`,
  `app:billing:rate`, `app:billing:report [--from --to]`, `app:billing:reprice`.
- **Схема БД** (`migrations/Version20261027151438.php`): `providers`, `models`,
  `prices`, `usage_events` + natural-unique-индексы.
- **Тесты** (`tests/Domain/`): PHPUnit-тесты `Money`, `RateCalculator`,
  `TokenBreakdown` (21 тест, 33 ассерции).

Проверка: полный сценарий приёмки TASK.md прогнан на чистой базе
(`make db-up && make migrate && app:prices:import ... && app:billing:rate &&
app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z`) —
вывод совпадает с примером; повторный импорт не меняет результат; тесты зелёные.
