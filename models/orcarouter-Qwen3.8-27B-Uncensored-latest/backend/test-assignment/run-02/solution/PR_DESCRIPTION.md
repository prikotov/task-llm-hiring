# Учёт расходов на LLM-токены

Реализовано консольное приложение (PHP 8.4+ / Symfony 7.4 / Doctrine ORM 3.7 /
PostgreSQL 17 / bcmath) для приёма двух потоков данных — **справочника цен**
(JSON) и **журнала использования** (JSONL) — точной десятичной арифметики,
учёта истории цен и оф-пиковых скидок, дедупликации событий, генерации
CSV-отчёта и (бонусом) ретро-коррекции цены.

Приложение проходит все пункты приёма, приведённые в `TASK.md`, и включает
бонусную команду ретро-коррекции.

---

## Что сделано

### Команды

| Команда | Назначение |
|---|---|
| `app:prices:import <file>` | Загрузка провайдеров, моделей и строк цен (idempotent upsert, `off_peak` провайдера). |
| `app:usage:import <file>` | Загрузка событий JSONL с дедупликацией по `(provider, provider_request_id)`. |
| `app:billing:rate` | Тарификация всех ещё pending-событий (включая повторные попытки unrated). `RATED=<n> UNRATED=<m>` в stdout, `UNRATED <provider>/<id> <ts> no_price` по каждой unrated в stderr. |
| `app:billing:report [--from=ISO][--to=ISO]` | CSV строго по спецификации (UTF-8, `,`, `\n`, unrated не попадают, `TOTAL` в конце). |
| `app:billing:reprice <file>` | **Бонус**: upsert строки/строк исправленных цен и пересчёт затронутых событий. Вывод: `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>`. |

Все команды выводят отчёт/результат в `stdout`, служебные сообщения — в
`stderr`. Код возврата: `0` — успех, `1` — ошибка (некорректный входной
файл, пропущенные обязательные аргументы, нарушение инвариантов).

### Тарификация (`src/Service/BillingEngine.php`)

- **Выбор цены** — наибольшая строка `valid_from ≤ ts`; `valid_from`
  включительно. Строка не найдена — событие `unrated` (status остаётся
  `pending`, в отчёт не попадает). Повторные запуски `app:billing:rate`
  перепроходят все `pending`.
- **Схема `per_1m_tokens`** — `Σ tokens_v · rate_v / 1 000 000`;
  **`per_1m_tokens_unified`** — `(Σ всех токенов) · rate_any / 1 000 000`.
- **Оф-пик** — окно `[start, end)` по времени суток UTC провайдера; окно
  может пересекать полночь (например, 16:30–00:30); стоимость всего
  события умножается на `multiplier` только если `ts` попал в окно.
  Окно задаётся на уровне провайдера и действует для всех его моделей.
- **Точность** — bcmath `scale = 20` для промежуточных величин; ровно одно
  `HALF_UP`-округление итогового события до 6 знаков
  (`App\Support\Decimal::roundMoney`). Тип `float` для денег не используется.

### Приём (`src/Service/PriceCatalogImporter.php`, `src/Service/UsageImporter.php`)

- Цены: idempotent upsert по `(provider, code)`, `(provider, code)` модели,
  `(provider, model, valid_from)` строки цен. Повторная загрузка
  корректного справочника не создаёт дублей.
- События: дедупликация по `(provider, provider_request_id)`; повторная
  доставка не обновляет уже записанное событие и не пересчитывает его.
  **Частичная ошибка**: валидные строки записываются, невалидные — каждая
  построчно в `stderr`, команда завершается кодом `1` (если хотя бы одна
  строка некорректна) или `0` (если все строки — дубли/валидные).

### Отчёт (`src/Service/ReportService.php`)

```
user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
TOTAL,,6200000,2300000,2500000,83333,33.662499
```

- Группировка по `(user, model)`, сортировка по `user`, затем по `model`
  (лексикографически, по возрастанию).
- `model` — `<provider>/<code>`.
- `--from` включительно, `--to` исключительно; без опций — все события.
- unrated-события в отчёт не попадают никогда.
- `TOTAL` — сумма по всей выборке (пустое поле `model`).

### Бонус: ретро-коррекция (`src/Service/RepriceService.php`)

`app:billing:reprice <file>` принимает файл того же формата, что и
`fixtures/prices.json` (секции `providers`/`models` могут отсутствовать —
они игнорируются), выполняет upsert исправленных строк цен по
`(provider, model, valid_from)` и пересчитывает события, тарифицировавшиеся
по этим строкам. Вывод строго две строки:

```
REPRICED_EVENTS=<n>
REPRICE_DELTA_USD=<x.xxxxxx>
```

`n` — число реально пересчитанных событий (новые и старые стоимости
сравниваются, строка в журнал пишется только при фактическом изменении),
`delta` — новая сумма − старая, 6 знаков. Пересчёт идемпотентен: повторное
применение той же коррекции даёт `REPRICE_DELTA_USD=0.000000`; отчёт после
reprice показывает пересчитанные суммы **без задвоений**.

### Схема БД (5 таблиц)

- `providers` — справочник, включая `off_peak_*` (`window_start`, `window_end`,
  `multiplier`) и `name`.
- `catalog_models` — справочник моделей (`provider_id`, `code`).
- `price_rows` — строки цен с `valid_from`, `unit`, `rates` (JSONB),
  `currency`; уникальный индекс по `(provider, model, valid_from)`.
- `usage_events` — события; status `pending|rated`, `cost_usd` NUMERIC(34,6),
  `price_row_id` (которую строку выбрали), уникальный индекс по
  `(provider, provider_request_id)` — дедупликация.
- `reprice_entries` — журнал пересчётов (event, old/new cost, old/new rate).

Уникальные индексы + `SELECT … FOR NONE` (без блокировок) — двойная
доставка события не приводит к двойному списанию.

---

## Архитектура

```
src/
├── Command/       5 Symfony-команд + BaseCommand (error handling)
├── Entity/        5 Doctrine-сущностей (Provider, Model, PriceRow,
│                  UsageEvent, RepriceEntry)
├── Exception/     ImportException (единый тип для ошибок импорта и валидации)
├── Service/
│   ├── BillingEngine.php        Чистая логика: расчёт стоимости события
│   ├── PriceCatalogImporter.php Upsert справочника цен
│   ├── UsageImporter.php        JSONL → events (дедупл. + частичная ошибка)
│   ├── RateProcessor.php        Выбор цены, применение, unrated
│   ├── ReportService.php        Готовит CSV
│   └── RepriceService.php       Бонус: ретро-коррекция
└── Support/
    ├── Csv.php          RFC-4180 writer (UTF-8, `,`, `\n`)
    ├── Decimal.php      bcmath: mul/add/div1m, roundMoney HALF_UP, formatMoney
    ├── OffPeak.php      Окно `[start, end)`, переход через полночь, `24:00`
    └── SqlDateTime.php  UTC-строка для raw SQL (`Y-m-d H:i:sP`)
```

Слои разделены: команды → services → (entities / support). Чистая бизнес-
логика (`BillingEngine`, `Decimal`, `OffPeak`) не зависит от БД и покрыта
unit-тестами.

---

## Ключевые решения и допущения

1. **Миграция — чистый SQL** (heredoc в `migrations/Version20261020120000InitialSchema.php`).
   Причина: Doctrine DBAL 4.5 Schema API (`nullable` column option) даёт
   ошибку на этапе генерации DDL; прямой SQL надёжнее и прозрачнее. `make migrate`
   разворачивает схему на чистой базе (подтверждено DROP DATABASE + migrate).

2. **`App\Support\SqlDateTime`** — обёртка для raw SQL-параметров.
   В DBAL 4.5 `DateTimeImmutable` нельзя передать как параметр
   `executeStatement()`, поэтому все даты в SELECT/UPDATE преобразуются в
   строку UTC (`Y-m-d H:i:sP`) до передачи.

3. **Dynamic WHERE в `ReportService::fetchRatedRows()`**.
   Postgres выбрасывает `SQLSTATE[42P18] Indeterminate datatype`, если один и
   тот же параметр фигурирует и в `IS NULL`, и в `>=` (тип не может быть
   выведен). Решение: WHERE строится динамически; параметр передаётся как
   `CAST(:p AS timestamptz)`.

4. **`wrapInTransaction`** вместо `->transaction()` (удалён в DBAL 4.5);
   `fetchAllAssociative()` вместо `query()->fetchAllAssociative()`.

5. **`fetchAssociative()` возвращает `false`** (не `null`), если строки нет, — все
   проверки «нет строки» сравнивают с `false`.

6. **Промежуточные `flush()` в `PriceCatalogImporter`** между блоками
   (провайдеры → модели → строки цен) — `ensureModel` ищет существующие
   провайдеры по SELECT, поэтому провайдеры должны быть записаны до моделей.

7. **Частичная ошибка JSONL** (решение): валидные строки записываются,
   невалидные — каждая построчно в `stderr` (форма: `Строка N: <msg>`);
   код возврата `1`, если была хотя бы одна некорректная строка, `0` иначе.
   (Задаче соответствует: «Код выхода 0 даже если все строки — дубли.
   Некорректная строка JSONL — ошибка, код 1.»)

8. **Окно оф-пика `start ≤ t < end`** (старт включительно, конец исключительно);
   окно может пересекать полночь; также поддержано `end = 24:00` (1440 мин)
   как удобный синтаксис для «до конца суток».

9. **`usage_events.status`** — enum-строка `pending|rated` (не отдельная
   таблица); `cost_usd` NULL, пока не тарифицировано; `price_row_id`
   сохраняет, какой строкой цен тарифицировано — это основа идемпотентного
   reprice.

10. **`tokens-*` — целые ≥ 0**; принят как число, так и десятичная строка
    (числовая строка конвертируется). Отрицательные/дробные/пустые значения —
    ошибка по строке.

11. **`valid_from`/`ts`** — обязательные, ISO-8601 UTC (`Z`); принимаем и
    без `Z` (в этом случае интерпретируем как локальное время и
    нормализуем в UTC при сохранении, чтобы выборка `valid_from ≤ ts`
    была корректна).

12. **Reprice** пересчитывает только события, чей `price_row_id` совпадает
    с id строки, которую заменяет коррекция. Если строка заменена, новые
    события, ещё не тарифицированные, будут вычислены уже с новыми
    значениями (повторный `app:billing:rate` тоже идемпотентен).

---

## Отступления от каркаса

- Добавлены **новые файлы** (23 файла `src/`, 1 миграция, 3 файла `tests/`,
  2 отчётных). Файлы каркаса (`TASK.md`, `AGENTS.md`, `README.md`,
  `fixtures/`, `composer.json`, `docker-compose.yml`, `Makefile`,
  `phpunit.xml.dist`) **не изменялись** — их менять не пришлось.
- **Новых зависимостей** не добавлено — все требования уже в `composer.json`
  (Symfony, Doctrine ORM, bcmath, Doctrine Migrations).
- **Тесты** (`tests/`) — 25 unit-тестов (31 ассертация), покрывают:
  `Decimal::roundMoney` (вкл. HALF_UP), `OffPeak` (вкл. полночь),
  `BillingEngine` (per_token, unified, off-peak, zero, e1/e2/e3 из TASK.md).
  Запуск: `vendor/bin/phpunit` (или `make test`).

---

## Как проверяли

Все шаги выполняются в этом репозитории; PostgreSQL 17 на `127.0.0.1:5544`
(пользователь `app`, БД `app`).

```bash
# 1) Чистая база + миграция
make db-up
PGPASSWORD=app psql -h 127.0.0.1 -p 5544 -U app -d postgres -c "DROP DATABASE app;"
make migrate

# 2) Импорт + тарификация + отчёт
bin/console app:prices:import  fixtures/prices.json
bin/console app:usage:import   fixtures/usage.jsonl
bin/console app:billing:rate   # -> "RATED=3 UNRATED=0"

# 3) Отчёт байт-в-байт (сравнить с TASK.md / expected.txt)
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# user,model,tokens_input,...
# u-alice,prism/lumen-8,...     14.262499
# u-alice,sfera/gyro-pro,...    8.400000
# u-bob,prism/lumen-8,...       11.000000
# TOTAL,,6200000,...            33.662499

# 4) Идемпотентность
bin/console app:prices:import  fixtures/prices.json   # повтор
bin/console app:usage:import   fixtures/usage.jsonl   # повтор — 0 новых
bin/console app:billing:rate   # -> "RATED=0 UNRATED=0"

# 5) Unrated (нет строки цен)
echo '{"provider":"ghost","provider_request_id":"g1","model":"nope","user":"u-x",
 "ts":"2026-09-15T17:00:00Z","tokens_input":100,"tokens_output":100,
 "tokens_cache_read":0,"tokens_cache_write":0}' > /tmp/ghost.jsonl
bin/console app:usage:import /tmp/ghost.jsonl
bin/console app:billing:rate   # stdout: RATED=0 UNRATED=1; stderr: UNRATED ghost/g1 … no_price

# 6) Частичная ошибка JSONL (валидную строку запишем, невалидные — в stderr)
printf '%s\n' \
'{"provider":"prism","provider_request_id":"e7","model":"lumen-8","user":"u-zed",
   "ts":"2026-09-15T10:00:00Z","tokens_input":1000000,"tokens_output":0,
   "tokens_cache_read":0,"tokens_cache_write":0}' \
'{"provider":"prism","model":"lumen-8","user":"u-zed",
   "ts":"2026-09-15T10:00:00Z","tokens_input":0,"tokens_output":0,
   "tokens_cache_read":0,"tokens_cache_write":0}' > /tmp/partial.jsonl
bin/console app:usage:import /tmp/partial.jsonl
# stderr: Строка 2: …  |  Принято: 1, Ошибки: 1    ;  exit=1

# 7) Бонус: reprice (input 1.5 -> 2.0) — e2: 11 -> 12, дельта 1.0
echo '{"prices":[{"provider":"prism","model":"lumen-8",
  "valid_from":"2026-09-15T12:00:00Z","currency":"USD","unit":"per_1m_tokens",
  "rates":{"input":"2.000000","output":"8.000000",
           "cache_read":"0.100000","cache_write":"1.000000"}}]}' > /tmp/corr.json
bin/console app:billing:reprice /tmp/corr.json
# REPRICED_EVENTS=1
# REPRICE_DELTA_USD=1.000000

# Повтор той же коррекции идемпотентен:
bin/console app:billing:reprice /tmp/corr.json
# REPRICED_EVENTS=1
# REPRICE_DELTA_USD=0.000000

# 8) Unit-тесты
vendor/bin/phpunit   # или: make test   # 25 тестов, 31 ассертация — OK
```

---

## Не было сделано и почему

- **HTTP API / UI / аутентификация / платежи** — по явному указанию задания
  («Не делать»).
- **Очереди / воркеры** — задача консольная, батч-пересчёт асинхронно не нужен.
- **Дробные токены** — задание требует целых ≥ 0, дедупликация и цена
  «за 1M токенов» предполагают целые.
- **Мониторинг / метрики** — приложение консольное, нет необходимости.
