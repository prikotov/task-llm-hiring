# Учёт расходов на LLM-токены

Реализованы все команды из TASK.md и бонусная `app:billing:reprice`.

## Что сделано

### Доменная модель (entity-классы в `src/Entity`)
| Сущность | Таблица | Ключевые атрибуты |
|----------|---------|--------------------|
| `Provider` | `provider` | `code` (уник.), `name`, оф-пик: `off_peak_window_start/end`, `off_peak_multiplier` (`null` — нет скидки) |
| `Model` | `model` | `(provider, code)` — уникальная |
| `PriceRow` | `price_row` | `unit` (`per_1m_tokens` / `per_1m_tokens_unified`), ставки `rate_input/output/cache_read/cache_write` или `rate_any` (decimal, 6 знаков), `valid_from` (`datetime_immutable`), уникальность `(model, valid_from)`, `currency` |
| `UsageEvent` | `usage_event` | `provider`, `provider_request_id` (уникальная пара с `provider`), `model`, `user`, `ts`, 4 столбца `tokens_*`, `status` (`new`/`unrated`/`rated`), `cost_usd` (decimal 20.6), ссылка на применённую `PriceRow` |

### Команды
| Команда | Поведение |
|---------|-----------|
| `app:prices:import <file>` | Идемпотентный upsert провайдеров/моделей/строк цен; валидация JSON, схем и ставок; некорректный JSON — код 1 |
| `app:usage:import <file>` | Импорт JSONL; дубли по `(provider, provider_request_id)` игнорируются; некорректная строка — код 1 |
| `app:billing:rate` | Тарифицирует все не-`rated` (включая повторные попытки `unrated`), в stdout — `RATED=.. UNRATED=..`, в stderr — по строке `UNRATED <provider>/<id> <ts> no_price` |
| `app:billing:report [--from=.. --to=..]` | CSV в stdout: заголовок, строки по `(user, "provider/model")` в порядке возрастания, `TOTAL`; период `[from, to)`; unrated — никогда |
| `app:billing:reprice <file>` | Применяет исправленные строки (upsert по `(provider, model, valid_from)`), пересчитывает события, тарифицировавшиеся **заменёнными** строками; в stdout — `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>` (6 знаков) |

### Ключевые решения
- **Точность.** Все деньги — строки `decimal` + bcmath (масштаб 16 для промежуточных значений). `bcdiv` с отрицательным масштабом недоступен в данной сборке PHP (8.5/bcmath), поэтому HALF_UP-округление до 6 знаков сделано явно: `floor(x*10^6 + 0.5)/10^6` в `App\Domain\Money::round6()`. Единственное округление — итог события; суммы отчёта — сумма уже округлённых значений.
- **Выбор цены.** Для модели берётся строка с наибольшим `valid_from ≤ ts` (включительно).
- **Оф-пик.** Окно `[start, end)` поддерживается как в пределах суток, так и через полночь; время — UTC.
- **Unrated.** Событие остаётся `unrated`, при каждом `rate` повторяется попытка; в отчёт не попадает.
- **Reprice.** Пересчёт в транзакции: `REPRICED_EVENTS` — число событий, чья цена была **заменена** (новые строки без старых не учитываются), `REPRICE_DELTA_USD` — разность сумм в 6 знаках.

### Слой служб (`src/Service`)
`PricesImporter`, `UsageImporter`, `RatingService`, `ReportService`, `RepricingService`, `PriceFields` (общий разбор строки цен). Валидация входных данных — `App\Shared\InputValidator`. Ошибки импорта — `App\Shared\ImportException` (вывод в stderr, код 1).

### Тесты
`tests/MoneyTest`, `tests/OffPeakWindowTest`, `tests/BillingCalculatorTest` — 11 тестов, 36 assert-ов.

## Как проверяли

```bash
cp .env.example .env
make db-up
make migrate

# 1) основной сценарий
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
#   → сверка с эталоном из TASK.md (совпадает)

# 2) идемпотентность
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
#   → тот же отчёт

# 3) ошибки
echo '{bad' > /tmp/bad.json && bin/console app:prices:import /tmp/bad.json; echo $?
#   → 1, сообщение в stderr

# 4) оф-пик и unrated (временные данные)
printf '... "ts":"2026-09-15T17:00:00Z" ...\n' > /tmp/op.jsonl
bin/console app:usage:import /tmp/op.jsonl && bin/console app:billing:rate
#   → unrated в stderr, отчёт отражает скидку

# 5) reprice
bin/console app:billing:reprice /tmp/reprice.json
#   → REPRICED_EVENTS / REPRICE_DELTA_USD; отчёт без задвоений

# 6) юнит-тесты
make test
```

## Отступления от каркаса и допущения

| Пункт | Обоснование |
|-------|-------------|
| Новый файл `src/Entity/UsageEventStatus.php` и перечисления статусов | Читаемость и защита от опечаток |
| `UsageEvent.status` хранит строку | Проще для миграции; допустимые значения ограничены константами |
| `REPRICE_DELTA_USD` всегда в 6 знаках (через `Money::format6`) | Требование TASK.md (6 знаков) |
| В `app:billing:reprice` файл коррекции должен содержать `prices` (секции `providers`/`models`) | Задание: провайдер/модель создаются только через `prices:import` |
| `make migrate` — создание схемы (по умолчанию, как в Makefile) | Не отступает от каркаса |
| Файл миграции `migrations/Version20261027000000.php` | Развёртывание схемы на чистой базе |
| Столбец `user` в `usage_event` переименован в `user_name` | `user` — зарезервированное слово Postgres 12+ (иначе SQL-ошибка) |
| `Money::round6()` вручную, без `bcdiv(.., -6)` | Данная сборка PHP не принимает отрицательный масштаб у `bcdiv`; результат идентичен HALF_UP |
