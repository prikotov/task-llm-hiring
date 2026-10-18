# PR — биллинг-конвейер: импорт, тарификация, отчёт и ретро-коррекция цены

Реализовано всё из `TASK.md`, включая бонус (команда `app:billing:reprice`).

## Что сделано

### Команды (все — через `bin/console`)
| Команда | Назначение |
|---|---|
| `app:prices:import <file>` | Идемпотентный upsert справочника (провайдеры, модели, строки цен) из JSON |
| `app:usage:import <file>` | Приём событий из JSONL, дедупликация по `(provider, provider_request_id)` |
| `app:billing:rate` | Тарификация не рассчитанных событий, повторные попытки для `unrated` |
| `app:billing:report` | CSV-отчёт по рассчитанным событиям за период `--from/--to` |
| `app:billing:reprice <file>` | **Бонус.** Ретро-коррекция цены: upsert строки, пересчёт, журнал |
| `app:billing:status` | Счётчики событий по статусам (диагностика) |

### Архитектура (PSR-4, `App\…`, все новые классы — в `src/`)
- `src/Entity/*` — доменные сущности: `Provider`, `Model`, `PriceRow`,
  `UsageEvent`, `RepriceRun`, `RepriceEventCorrection`.
- `src/Billing/Pricing/` — схема тарификации (`PriceUnit`) и калькулятор
  (`PricingCalculator`): `per_1m_tokens` (4 вида токенов) и
  `per_1m_tokens_unified`.
- `src/Billing/OffPeak/` — окно оф-пика (`OffPeakWindow`, переход через
  полночь) и выбор множителя (`OffPeakPricing`).
- `src/Application/*` — сервисы команд + DTO-сводки (отчёт, тарификация,
  импорт, reprice).
- `src/Command/*` — консольные команды (общая база
  `AbstractBillingCommand`: ошибки → stderr + exit 1).
- `src/Support/*` — строгие парсеры: `StrictIsoUtc` (ISO-8601 с заглавной Z),
  `TimeOfDay` (HH:MM), `SpecReader` (валидация полей), `JsonFile`,
  `DecimalLiteral`.
- `src/Shared/` — `Decimal` (bcmath-арифметика money6), `BadInputException`.

### Деньги и время — ключевые решения
- **Точная арифметика** через `bcmath` (`Decimal`). Никакого бинарного float.
- **Единственное округление** — итоговой стоимости *события* до 6 знаков,
  `HALF_UP`. Окружение: сначала применяется множитель оф-пика, затем одно
  финальное округление (тесты проверяют «полу-микро-доллар вверх»).
- **Отчёт** суммирует уже округлённые стоимости событий без повторного
  округления слагаемых (тест с `14.262499 + 8.400000 + 11.000000 = 33.662499`).
- **Выбор цены**: строка модели с наибольшим `valid_from <= ts` (включительно):
  цены, вступившие в силу после события, на него не влияют (тест на e2: строка
  `2026-09-15T12:00:00Z` применяется к событию ровно в `12:00`).
- **Валюта**: поле `currency` обязательно и хранится в строке цены (в фикстурах
  задачи всегда `USD`; другие значения валидации не проходят — поле
  зарезервировано под будущие валюты); `cost_usd` приводится до 6 знаков.
- **Строгий вход**: ISO-8601 строго с заглавной `Z`; HH:MM; целые
  неотрицательные токены; множитель/цены — положительные десятичные строки.
  Любая некорректная строка → сообщение в stderr и `exit 1`, без частичных
  записей.

### Идемпотентность и дедупликация
- Провайдер — по `code`; модель — по `(provider, code)`; строка цены — по
  `(provider, model, valid_from)`. Повторная загрузка тех же сумм — no-op
  (проверено: повторный import + rate + report не меняет результат).
- Дубли использования по `(provider, provider_request_id)` игнорируются
  целиком (ни обновлений, ни доначислений).

### Отчёт и вывод
- CSV строго в **stdout** (UTF-8, `,`, `\n`), колонки ровно как в примере
  TASK.md, unrated-события никогда не попадают в отчёт.
- Сводка `rate` — в stdout (`RATED=<n> UNRATED=<m>`), диагностика unrated —
  в stderr (`UNRATED <provider>/<id> <ts> no_price`).
- Остальные служебные сообщения — в **stderr**. Успех → exit 0, ошибка → exit 1.

### Бонус: ретро-коррекция `app:billing:reprice`
- Файл коррекции — тот же формат, но используются только строки `prices`:
  провайдер и модель должны существовать **в базе** (секции
  `providers`/`models` в файле не обязательны / могут быть пустыми).
- Upsert строки по `(provider, model, valid_from)`; пересчитываются
  `rated`-события, тарифицированные по заменённой строке (`priceRow` события
  не меняется, `ratedAt` не трогается).
- **Журнал**: `RepriceRun` (исходный файл, ключи строк, число событий,
  дельта, время) + `RepriceEventCorrection` на каждое событие со снимками
  «до/после» (old/new: unit, rates, cost), чтобы аудит был самодостаточен.
- Вывод — ровно две строки: `REPRICED_EVENTS=<n>` и
  `REPRICE_DELTA_USD=<x.xxxxxx>` (новая − старая сумма, 6 знаков).
- Повторный запуск той же коррекции — те же события, дельта `0.000000`
  (идемпотентно), отчёт не задваивается.

## Изменённые / добавленные файлы
Только добавления в `src/`, `tests/`, `migrations/`; каркасные файлы
(включая `composer.json`, `docker-compose.yml`, `phpunit.xml.dist`) не
трогались. Зависимости **не добавлены** (все уже были в `vendor/`).

- Сущности: `src/Entity/{Provider,Model,PriceRow,UsageEvent,RepriceRun,RepriceEventCorrection}.php`
- Биллинг: `src/Billing/Pricing/{PriceUnit,PricingCalculator}.php`,
  `src/Billing/OffPeak/{OffPeakWindow,OffPeakPricing}.php`
- Сервисы + DTO: `src/Application/{PricesImportService,UsageImportService,RateService,ReportService,RepriceService}.php`,
  `src/Application/Dto/{PricesImportSummary,UsageImportSummary,RatingSummary,BillingReport,EntityTally,RepriceSummary}.php`
- Команды: `src/Command/{AbstractBillingCommand,PricesImportCommand,UsageImportCommand,BillingRateCommand,BillingReportCommand,BillingRepriceCommand,BillingStatusCommand}.php`
- Поддержка: `src/Support/{StrictIsoUtc,TimeOfDay,SpecReader,JsonFile,DecimalLiteral}.php`
- Общие: `src/Shared/{Math/Decimal.php,Exception/BadInputException.php}`
- Тесты: `tests/Unit/{Decimal,OffPeak,PricingCalculator,StrictIsoUtc,TimeOfDay}Test.php`
- Миграция: `migrations/Version20261017064517.php`

## Ключевые решения и допущения
- **Окно оф-пика** — начало включительно, конец исключительно (по TASK.md);
  заданное как интервал времени суток UTC: если конец раньше начала —
  переход через полночь (например, 16:30–00:30), иначе — интервал в рамках
  одних суток; равные границы — окно пустое, скидка не применяется (допущение,
  зафиксировано в тестах `OffPeakTest`);
- **Отчёт** группирует по `(user, модель)`; строка `TOTAL` — сумма по всем
  сущностям. Период `--from..--to` — `from` включительно, `to` исключительно,
  по `ts` события (UTC).
- **`status`** — дополнительная диагностическая команда (не требует TASK.md,
  но полезна); все её сообщения — в stderr, exit 0.
- **Reprice**: механизм пересчёта — «переписать события + журнал корректировок»
  (TASK.md позволяет выбрать); компенсирующих записей нет — отчёт агрегирует
  текущие `rated`-события, задвоения исключены.
- **Money6**: `cost_usd` всегда ровно 6 знаков (`0.000000` при нуле).
- **PostgreSQL 17** — только через ORM/DBAL + миграции (без raw-DDL).

## Как проверяли (самопроверка)
Все команды реально прогнаны.

1. Чистая БД: `make db-up`; схема — `make migrate` (по умолчанию).
2. Импорты:
   ```
   bin/console app:prices:import fixtures/prices.json   # OK
   bin/console app:usage:import  fixtures/usage.jsonl    # OK
   bin/console app:billing:rate                             # RATED=3 UNRATED=0
   ```
3. Отчёт байт-в-байт совпадает с примером TASK.md:
   ```
   bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
   ```
4. Идемпотентность: повторный импорт + rate + report → результат
   не меняется (проверено сравнением выводов).
5. Бонус (reprice): файл коррекции с новой строкой `prism/lumen-8
   valid_from=2026-09-15T12:00:00Z` применяется, stdout ровно
   `REPRICED_EVENTS=1` / `REPRICE_DELTA_USD=…`, отчёт показывает
   пересчитанную сумму без задвоений; повторный запуск той же коррекции —
   дельта `0.000000`.
6. Коды возврата: успех → 0; некорректный JSON/JSONL, не найденная
   модель/провайдер, пустой `prices`, отрицательные токены, не-`Z` в дате,
   битое время суток → `exit 1` с ошибкой в stderr.
7. Юнит-тесты: `vendor/bin/phpunit` → **OK (33 tests, 65 assertions)**,
   включая границы оф-пика, half-up на пол-микро-доллара, суммы без
  повторного округления, строгий парсинг дат/времени.

### Быстрый воспроизводимый прогон
```bash
make db-up                      # поднять PostgreSQL 17 (порт по умолчанию 5544)
make migrate                    # создать/перезалить схему
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import  fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
vendor/bin/phpunit              # юнит-тесты
```
