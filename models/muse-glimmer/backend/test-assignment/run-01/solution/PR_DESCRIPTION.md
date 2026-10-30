# Реализация учёта расходов на LLM-токены

## Что сделано
- Создана схема БД через Doctrine Migrations: providers, models, prices, usage_events.
- Реализованы сущности Provider, Model, Price, UsageEvent с Doctrine ORM.
- Команды:
  - `app:prices:import <file>` — загрузка провайдеров, моделей и цен с upsert по ключам.
  - `app:usage:import <file>` — импорт JSONL событий с дедупликацией по (provider, provider_request_id).
  - `app:billing:rate` — тарификация нероценированных событий: выбор цены с `valid_from <= ts`, учёт off-peak окна, расчёт по схемам `per_1m_tokens` и `per_1m_tokens_unified`, округление HALF_UP до 6 знаков, вывод `RATED/UNRATED`, сообщения об unrated в stderr.
  - `app:billing:report [--from --to]` — CSV отчёт с группировкой по user/model, сумма токенов и стоимостей, итоговая строка TOTAL.

## Ключевые решения и допущения
- Деньги — BCMath с масштабом 32, округление реализовано вручную HALF_UP, float не используется.
- off-peak проверяется по минутам UTC, окно может переходить через полночь.
- Цены хранятся в отдельной таблице, поиск — `MAX(valid_from <= ts)`.
- Дубли событий игнорируются при импорте.
- Unrated события остаются с `cost_usd = NULL` и повторно пробуются при каждом запуске `rate`.
- Поле user переименовано в `user_id` в БД во избежание конфликта с зарезервированным словом PostgreSQL.

## Проверка
```bash
make db-up
make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```
Вывод совпадает байт-в-байт с примером задания. Повторный импорт не меняет данные.

## Отступления
- Колонка `user` переименована в `user_id` для совместимости с PostgreSQL.
- Использован тип `datetimetz_immutable` для дат, `time` для окна off-peak.
- Бонус `app:billing:reprice` не реализован — в задании помечен как опциональный.
