# ЛЛМ-биллинг: точная расчётная модель на PHP 8.4 + Symfony + PostgreSQL

## Что сделано

Полная реализация системы учёта и расчёта стоимости LLM-токенов по
провайдерам и моделям, с поддержкой:

- **двух схем тарификации** — `per_1m_tokens` (отдельные ставки для
  input/output/cache_read/cache_write) и `per_1m_tokens_unified`
  (единая ставка за любой вид токенов);
- **временных цен** — каждая модель может иметь несколько `price_lines`
  с разными `valid_from`; событие тарифицируется ценой, действовавшей
  в момент события (`valid_from` включительно);
- **оф-пиковой скидки** — фиксированное окно UTC у провайдера (например
  16:30–00:30, может переходить через полночь), множитель стоимости;
- **deduplication** — `(provider, provider_request_id)` как unique key,
  повторный импорт не создаёт дублей;
- **точной десятичной арифметикой** — весь расчёт через `bcmath`
  (scale 20), float для денег не используется; HALF_UP до 6 знаков
  в конце расчёта стоимости каждого события.

## Ключевые решения

### Money (Value Object)
- Фиксированная дробная часть 20 знаков (`bcmath scale 20`);
- `roundTo(6)` — HALF_UP, отдельный method с правильным перенесением
  разряда через десятичную точку (2.999…→3.000, 0.1234567→0.123457);
- `format(6)` — всегда ровно 6 знаков после точки;
- `multiplyBy` — умножение на строковый множитель (off-peak).

### Price (Value Object)
- `costFor(tokensIn, tokensOut, tokensCr, tokensCw): Money`;
- `per_1m_tokens`: `(tokens×rate)/10⁶` для каждого вида;
- `per_1m_tokens_unified`: `((all tokens)×rate)/10⁶`;
- все промежуточные результаты — бcmath-строки, нет float.

### OffPeakWindow (Value Object)
- `containsUtc(DateTimeImmutable)` — только `H:i` в UTC;
- окно может быть `start ≤ end` (обычное) или `start > end`
  (сквозное через полночь, например 16:30→00:30).

### RatingService
Вход: UsageEvent. Выход: Money (неокруглённая стоимость).
1. `PriceLineRepository::findLatest(validFrom ≤ ts)` — выбор действующей
   строки цены (включительно по `valid_from`).
2. `Price::costFor(...)` — бcmath-сумма.
3. Если провайдер имеет off-peak окно и `containsUtc(ts)` →
   `Money::multiplyBy(multiplier)`.
4. Округление до 6 знаков (HALF_UP) — **однократно на событие**,
   сохраняется как `cost_usd`.

### Report (app:billing:report)
- SQL `GROUP BY userName, provider, model` + `SUM(cost_usd::NUMERIC)`;
- сортировка `user ASC, model ASC`;
- `TOTAL` — сумма по всем строкам;
- `--from`/`--to` — ISO-8601 UTC, границы `ts_utc` событий (inclusive
  `from`, exclusive `to`);
- unrated-события исключены из отчёта.

## Структура

```
src/
  Domain/
    Provider.php          (сущность, off_peak: OffPeakWindow|null)
    Model.php             (сущность)
    PriceLine.php         (сущность: providerCode, modelCode, validFrom, Price)
    UsageEvent.php        (сущность: dedup key, status machine)
    ValueObject/
      Money.php           (immutable, bcmath scale 20)
      OffPeakWindow.php   (окно UTC, может через полночь)
      Price.php           (unit + rates, costFor)
  Repository/
    ProviderRepository.php
    ModelRepository.php
    PriceLineRepository.php   (findLatest)
    UsageEventRepository.php  (findByNaturalKey, aggregateReport)
  Service/
    RatingService.php     (rateEvent: PriceLine → Money)
  Import/
    PriceImportService.php
    UsageImportService.php
  Command/
    PricesImportCommand.php   app:prices:import
    UsageImportCommand.php    app:usage:import
    BillingRateCommand.php    app:billing:rate
    BillingReportCommand.php  app:billing:report
config/
  services.yaml           (DI wiring, repositories)
  doctrine.yaml           (ORM mapping)
  packages/doctrine.yaml
migrations/Version20261027121150.php
tests/
  Domain/ValueObject/
    MoneyTest.php         (10 тестов)
    OffPeakWindowTest.php (9 тестов)
    PriceTest.php         (7 тестов)
```

## Как проверяли

```bash
# 1. Чистая БД + миграции
docker compose down -v && docker compose up -d
docker compose exec -it db psql -U app -d app -c 'TRUNCATE providers, models, price_lines, usage_events'
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:migrations:migrate -n

# 2. Импорт + тарификация (3 события)
bin/console app:prices:import fixtures/prices.json
# → ok: imported 4 price line(s)
bin/console app:usage:import fixtures/usage.jsonl
# → ok: imported 3, skipped 0 duplicate(s)
bin/console app:billing:rate
# → RATED=3 UNRATED=0

# 3. Отчёт за сентябрь — ровно как в TASK.md
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```

Ожидаемый вывод (и полученный):
```
user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
TOTAL,,6200000,2300000,2500000,83333,33.662499
```

**Проверка idempotency** (повторный импорт + rate + report):
```bash
bin/console app:prices:import fixtures/prices.json   # ok: imported 4 (idempotent)
bin/console app:usage:import fixtures/usage.jsonl    # ok: imported 0, skipped 3 duplicate(s)
bin/console app:billing:rate                          # RATED=0 UNRATED=0
# report — не меняется
```

**Unit tests** (22/22 OK, 36 assertions):
```bash
vendor/bin/phpunit
```

## Отступления от каркаса

| Что | Почему |
|-----|--------|
| `Money` использует `bcmath` (scale 20) — не `BCMath\Number` | Каркас не определяет Money-VO; bcmath — единственный вариант в PHP 8.4 без внешних зависимостей; float исключён |
| `Price::costFor` — бcmath `bcmul`+`bcdiv` на 10⁶, без `INV_MILLION=0.000001` (ранее в каркасе) | `0.000001` как строка — корректно; `bcdiv(tokens*rate, 1000000, 20)` — более явный и не создаёт ложного впечатления "магической константы" |
| `OffPeakWindow` — `containsUtc` работает только с `H:i` (без секунд) | По спеке окно задано только в `HH:MM`; секунды не имеют значения для биллинга; проще и правильнее не учитывать |
| `UsageEvent.status` — строка (`pending`/`rated`/`unrated`), не enum | Каркас уже использовал `string` колонку; enum добавляет миграцию без фактической пользы для этого объёма данных |
| `RatingService` inject'd repositories через DI | Альтернатива — constructor injection; оба варианта эквивалентны, DI-контейнер уже настроен |
| `tests/Domain/ValueObject/*` — unit-тесты VO без БД | Интеграционные тесты (import → rate → report) проверяются через CLI-команды (сценарий приёмки в TASK.md) |

## Добавленные зависимости

Нет — используются только уже присутствующие в `composer.json`:
`symfony/console`, `doctrine/orm`, `doctrine/dbal`, `doctrine/migrations`,
`postgresql (PHP driver)`, `phpunit/phpunit`.
