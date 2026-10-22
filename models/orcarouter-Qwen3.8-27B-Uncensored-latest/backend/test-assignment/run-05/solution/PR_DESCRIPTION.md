## Что сделано

Реализован учёт стоимости LLM-событий по историческим ценам тарифного плана
(PHP 8.4+, Symfony, Doctrine/PostgreSQL), строго соблюдены требования к точности
арифметики (bcmath) и формату вывода.

### Слой домена (`src/Domain`)
- `Provider` — провайдер; тарифный период (off-peak: окно + множитель) хранится
  здесь, метод `isInOffPeak()` поддерживает окно, переносящееся через полночь.
- `Model` — модель в рамках провайдера.
- `PriceRow` — версия ставок с `valid_from`, валюта, единица тарификации,
  ставки хранятся в JSON-объекте `rates` (ключи: `input|output|cache_read|cache_write`
  для `per_1m_tokens`; `any` — «любая единица», применяется к сумме всех токенов).
- `UsageEvent` — событие запроса: токены по типам, `ts` (timestamptz), статус
  (unrated/rated), финальная стоимость `cost_usd` (VARCHAR(32), строка), ссылка на
  применённую `PriceRow` — именно она делает возможным пересчёт (бонус).
- `CostCalculator` — чистая функция расчёта: `Σ(токены × ставка)/1e6`, применение
  множителя off-peak, окончательный HALF-UP-округление до 6 знаков.

### Поддержка (`src/Support`)
- `Money` — вся арифметика на bcmath: суммирование событий — **без** округления,
  событие округляется единожды до 6 знаков (half-up); отчёт суммирует уже
  окончательные значения событий (проверено на образце: сумма округлений = 33.662499).
- `ImportError` — типизированная ошибка импорта с сообщением.
- `PriceFile` — разбор/валидация JSON цен (provider, model, unit, currency,
  rates) с понятными сообщениями об ошибке.
- `IsoTime` — парсинг ISO-8601 в UTC.

### Сервисы (`src/Service`)
- `PricesImportService` — атомарный импорт цен: авто-создание провайдеров/моделей,
  обновление тарифного периода, замена ставок idempotently по
  (provider, model, unit, valid_from).
- `UsageImportService` — idempotentный импорт JSONL по одному
  (уникальный ключ `provider + provider_request_id`, `ON CONFLICT DO NOTHING`,
  дубликаты — тихо skip, exit 0; невалидная строка — exit 1, файл не применяется).
- `BillingRater` — выбор ставки: **максимальный `valid_from` ≤ `ts`** события;
  unrated-события без цены остаются unrated (повторно обрабатываются при повторном
  запуске после импорта цен).
- `ReportService` — строка на пару (user, model), лексикографическая сортировка,
  строка TOTAL, формат CSV: UTF-8, `,`, LF, `cost_usd` ровно 6 знаков.
- `RepriceService` (бонус) — замена ставок по тому же формату файла цен, пересчёт
  всех событий, на эти ставки ссылающихся (FK `price_row_id`), вывод
  `REPRICED_EVENTS=<n>` / `REPRICE_DELTA_USD=<delta>` (half-up, 6 знаков).

### Команды (`src/Command`)
| Команда | Назначение |
|---|---|
| `bin/console app:prices:import <file>` | Импорт цен (JSON) |
| `bin/console app:usage:import <file>` | Импорт событий (JSONL) |
| `bin/console app:billing:rate` | Расчёт стоимости unrated-событий |
| `bin/console app:billing:report [--from ISO] [--to ISO]` | Отчёт (CSV в stdout) |
| `bin/console app:billing:reprice <file>` | Бонус: пересчёт по новым ставкам |

### Слой БД
- Сущности — Doctrine ORM; числовые значения — точные типы (`BIGINT` для токенов,
  `VARCHAR(32)` для денежных сумм, JSON для ставок).
- **timestamptz**: добавлен собственный DBAL-тип (DBAL 4 не предоставляет) и
  слушатель, фиксирующий сессию в `TZ='UTC'`; конфигурация расширена в
  `config/packages/doctrine.yaml` / `config/services.yaml` (минимально, под задачу).
- Схема: `migrations/Version20261020165104.php` — создаётся `make migrate`
  (проверено на чистом контейнере Postgres 17).

### Тесты (`tests/`)
- `MoneyTest` — точность бcmath-суммирования и half-up.
- `OffPeakTest` — включённость окна, включая «перенос» через полночь и границы.
- `CostCalculatorTest` — расчёт per-token, any, off-peak, округление.

## Как проверить

```bash
make db-up && make migrate
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# сверка:
#  u-alice,prism/lumen-8,1200000,...,14.262499
#  u-alice,sfera/gyro-pro,3000000,...,8.400000
#  u-bob,prism/lumen-8,2000000,...,11.000000
#  TOTAL,,6200000,2300000,2500000,83333,33.662499

# идемпотентность: повтор imports+rate даёт тот же отчёт, exit 0
# unrated: импорт событий до импорта цен → rate → строки UNRATED... no_price
# reprice: bin/console app:billing:reprice <file> → REPRICED_EVENTS / REPRICE_DELTA_USD

vendor/bin/phpunit
```

## Отклонения от каркаса (и почему они нужны)
- `config/packages/doctrine.yaml` — регистрация DBAL-типа `timestamptz`.
- `config/services.yaml` — слушатель, принудительно устанавливающий сессию в UTC.
- Оба изменения минимальны, обусловлены требованиями ТЗ (timestamptz) и не
  затрагивают существующие сервисы каркаса.

## Зависимости
Новых внешних пакетов не добавлено (только то, что уже было в каркасе, включая bcmath).
