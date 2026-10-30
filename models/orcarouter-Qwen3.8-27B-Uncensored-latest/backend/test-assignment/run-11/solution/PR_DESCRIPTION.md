# PR: Учёт расходов на LLM-токены

## Что сделано

Реализовано консольное приложение на Symfony/Doctrine для учёта расходов
на LLM-токены по требованиям из `TASK.md`.

**Команды `bin/console`:**

| Команда | Назначение |
|---|---|
| `app:prices:import <file>` | Идемпотентный upsert провайдеров, моделей, строк цен |
| `app:usage:import <file>` | Импорт JSONL-событий с дедупликацией по (provider, request_id) |
| `app:billing:rate` | Тарификация pending/unrated событий, вывод `RATED=n UNRATED=m` |
| `app:billing:report --from=… --to=…` | CSV-отчёт по (user, model), `TOTAL` в конце |
| `app:billing:reprice <file>` *(бонус)* | Ретро-коррекция строк цен — `REPRICED_EVENTS` / `REPRICE_DELTA_USD` |

**Приёмка:**
- Фикстуры `fixtures/prices.json` + `fixtures/usage.jsonl` воспроизводят
  пример из TASK.md **байт-в-байт** (проверено командой `app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z`).
- Повторный импорт + rate + report — результат не меняется (идемпотентность).

## Архитектура

```
src/
  Domain/
    Money.php                      — точная десятичная арифметика (bcmath\Number),
                                     float запрещён; roundedToMicros() — HALF_UP 6 знаков
    Provider.php                   — entity: code, name, off-peak params
    Model.php                      — entity: code, provider
    PriceRow.php                   — entity: valid_from, unit, rates (JSON)
    UsageEvent.php                 — entity: provider_code, provider_request_id, ts,
                                     tokens, cost, status (pending/rated/unrated)
    EventCostCalculator.php        — выбор схемы (per-token / unified),
                                     применение off-peak multiplier
    ImportDataException.php
    InvalidMoneyValueException.php
    DivisionByZeroMoneyException.php
  Repository/
    DomainRepository.php           — единая точка доступа к Doctrine ORM
  Application/
    Prices/PriceImportService.php  — upsert провайдеров/моделей/строк
    Usage/UsageImportService.php   — дедупликация (provider, request_id)
    Billing/BillingService.php     — rate(), reprice()
    Reports/ReportService.php      — агрегация по (user, model) в CSV
  Command/
    PricesImportCommand.php
    UsageImportCommand.php
    BillingRateCommand.php
    BillingReportCommand.php
    BillingRepriceCommand.php
```

## Ключевые решения и допущения

1. **Управление схемой** — `bin/console doctrine:schema:create` (без миграций).
   Схема создаётся из аннотаций entity-классов. Способ указан здесь;
   в `Makefile` можно добавить `make migrate` → `bin/console doctrine:schema:create`.

2. **Unrated-события** — не считаются ошибкой, остаются с `status='unrated'`,
   каждый запуск `app:billing:rate` повторно пытается их прайсу.
   Если цена так и не найдена — событие остаётся unrated.

3. **Округление** — ровно одно: стоимость каждого события (после применения
   off-peak multiplier) округляется HALF_UP до 6 знаков.
   Суммы отчёта складывают уже округлённые значения без повторного округления.

4. **Reprice** — меняет `price_row` на новую/обновлённую строку и пересчитывает
   cost. Дельта = новая сумма − старая сумма. События, не связанные с заменённой
   строкой, не затрагиваются.

## Изменённые файлы (от каркаса)

Ни один файл каркаса не изменён. Все файлы `src/` — новые.
Единственное дополнение — `.env.local` с строкой подключения к БД
(`DATABASE_URL="postgres://app:app@127.0.0.1:5544/billing_app?serverVersion=17&charset=utf8"`).

## Как проверяли

```bash
# 1. Очистка БД
PGPASSWORD=app psql -h 127.0.0.1 -p 5544 -U app -d billing_app \
  <<SQL
DROP SCHEMA public CASCADE;
CREATE SCHEMA public;
SQL

# 2. Схема
bin/console doctrine:schema:create

# 3. Заполнение
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# RATED=3 UNRATED=0

# 4. Отчёт (байт-в-байт совпадает с примером TASK.md)
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z

# 5. Идемпотентность — повторный цикл
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# RATED=0 UNRATED=0  (все уже рассчитаны)
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# результат идентичен

# 6. Ретро-коррекция (бонус)
bin/console app:billing:reprice /tmp/correction.json
# REPRICED_EVENTS=1
# REPRICE_DELTA_USD=-2.377083
```
