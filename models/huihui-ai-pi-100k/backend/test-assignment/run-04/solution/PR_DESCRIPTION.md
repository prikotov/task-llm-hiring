# Учёт расходов на LLM-токены

Реализован весь объём TASK.md, включая бонусную ретро-коррекцию цен.

## Что сделано

| Команда | Назначение |
| --- | --- |
| `bin/console app:prices:import <file>` | Идемпотентная загрузка провайдеров (т.ч. off-peak параметров), моделей и строк цен (пер-1M токенов или единый тариф); upsert по `(provider, model, valid_from)` |
| `bin/console app:usage:import <file>` | Загрузка из JSON Lines; дедупликация по `(provider_code, provider_request_id)`; события без цены в каталоге — `unrated` |
| `bin/console app:billing:rate` | Расчёт стоимости `unrated` событий (с повторными попытками); stdout: `RATED=n UNRATED=m`, diagnostic `UNRATED ... no_price` — в stderr |
| `bin/console app:billing:report --from=<UTC> --to=<UTC>` | CSV по `(user, модель)` + `TOTAL`; unrated-события не учитываются; byte-в-байт по образцу |
| `bin/console app:billing:reprice <file>` | (бонус) Применение исправленных строк цен по справочнику; пересчёт затронутых событий; stdout ровно `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>` |

### Ключевые решения

- **Деньги — только `bcmath` + `NUMERIC`**. Все промежуточные произведения и
  суммы — десятичные строки; единственное округление — `roundHalfUp` до 6
  знаков в конце (нет накопления погрешности на промежуточных этапах).
- **Время — `TIMESTAMP(0) WITHOUT TIME ZONE`**, значения всегда UTC.
  Разбор ISO-8601 (`Z` / смещение, долей секунд нет) в `App\Support\Iso8601`.
- **Тариф — по ближайшей строке**: `valid_from <= ts`, без верхнего bound —
  так как строки имеют `valid_from`, а не диапазон.
- **Off-peak** хранится у провайдера (окно `HH:MM`–`HH:MM`, множитель).
  Начало окна включается, конец не включается (`[start, end)`), окно может
  пересекать полночь; `start == end` — окно пустое.
- **Состояние расчёта** — `usage_events.status` (`unrated`/`rated`) + денормализованные
  `cost_usd` и FK на строку цены, поэтому `rate` по идее идемпотентен и безопасен
  при повторных запусках без задвоений; `report` агрегирует только `rated`.
- **Структура**: домен (`src/Domain`: `MoneyMath`, `TimeWindow`, `CostCalculator`),
  ingest-слой (`src/Ingest`: парсер каталога/событий и DTO), сервисы
  (`src/Billing`), консольные команды (`src/Command`) — без ORM-сущностей
  в доменных правилах (`src/Entity` — только ORM-обёртки).
- **Ретро-коррекция** (бонус): upsert строки цены по `(provider, model, valid_from)`
  (провайдер и модель обязаны существовать в базе — секции `providers`/`models`
  в файле могут отсутствовать или быть пустыми), затем пересчёт только событий,
  для которых применимой стала именно заменённая строка; дельта —
  новая минус старая сумма их стоимости; повторный `report` показывает
  пересчитанные суммы без задвоений.
- **Строки/отступы**: вывод команд соответствует спецификации; ошибки —
  человекочитаемые сообщения в stderr, exit code 1.

### Допущения (неоговорённые детали)

1. **Off-peak** применяется по провайдеру события (атрибут провайдера), а не
   по строке цены — в TASK он описан как свойство провайдера.
2. **Границы окна off-peak**: начало включается, конец нет
   (`23:00 <= t < 01:00`, при пересечении полуночи — союз `[start,24:00)` ∪ `[00:00,end)`).
3. **`RATED`/`UNRATED`** в `rate` — счётчики событий, обработанных в данном
   запуском; при наличии `unrated`-событий exit code остаётся 0
   (задание требует только диагностических строк в stderr).
4. **`REPRICED_EVENTS`** — число пересчитанных событий (те, для которых
   применимой стала заменённая строка), а не число событий с изменившейся
   стоимостью; дельта считается по старой стоимости всех пересчитанных.
5. **Модель в отчёте** — полная ссылка `provider/model` (например,
   `prism/lumen-8`), чтобы не сталкивались модели с одинаковыми code у
   разных провайдеров.
6. **Токены** — не отрицательные целые; `tokens_cache_read/write` — отдельно
   от `input/output` (в тарифе — отдельные ставки).
7. Повторный импорт того же файла — идемпотентен: строки не дублируются,
   уже обработанные события не пересчитываются заново (кроме `reprice`).

## Как проверяли

### Автоматически

```bash
make db-up        # PostgreSQL 17 (порт по умолчанию 5544)
make migrate      # схема на чистой базе
make test         # PHPUnit: 70 тестов (61 юнит + 9 интеграционных)
```

Проходят: полный приёмочный сценарий TASK.md (импорт → rate → report),
идемпотентность повторного импорта, unrated→rated, дедупликация,
off-peak (в т.ч. окно через полночь и границы), ретро-коррекция
и коды ошибок.

### Вручную (приёмка TASK.md)

```bash
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# RATED=3 UNRATED=0

bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# байт-в-байт совпадает с примером из TASK.md, TOTAL 33.662499

# Повторный импорт + rate + report — результат не меняется (идемпотентность)

# Бонус: ретро-коррекция (изменение input-ставки lumen-8 с 08-01: 3 → 4)
bin/console app:billing:reprice fixtures/reprice.json
# REPRICED_EVENTS=1
# REPRICE_DELTA_USD=1.200000
# после этого report показывает 15.462499 вместо 14.262499 (строка u-alice)
```

Ошибки валидации (некорректный JSON, несовпадение модель/провайдер,
плохое время в `--from/--to`, файл коррекции со ссылкой на несуществующую
модель) — человекочитаемое сообщение в stderr, exit code 1.

## Отступления от каркаса

Каркасные файлы (`composer.json`/`composer.lock`, `docker-compose.yml`,
`Makefile`, `phpunit.xml.dist`) не изменялись; новых зависимостей
не добавлено. Остальные изменения (новые файлы и два конфиг-файла)
перечислены ниже с обоснованием.

1. **`config/packages/framework.yaml`** — добавлен блок `when@test:` с
   `framework.test: true`. Нужен для `KernelTestCase` в интеграционных
   тестах (через `getContainer()` достаются сервисы-команды); в dev/prod
   не влияет.
2. **`config/packages/doctrine.yaml`** — добавлен параметр по умолчанию
   `env(TEST_DATABASE_URL)` и блок `when@test`, в котором в test-окружении
   `dbal.url` берётся из `TEST_DATABASE_URL` (база `app_test`), а в dev — как
   и раньше, из `DATABASE_URL` (`app`). Причины: (а) dotenv не перезаписывает
   переменные, заданные ранее загруженным `.env`, поэтому переопределение
   `DATABASE_URL` в `.env.test` не срабатывало — интеграционные тесты работали
   в dev-БД; (б) ядро не подключает dotenv в процессе phpunit, поэтому
   переменной нужен default в `parameters: env(...)`.
3. **`tests/bootstrap.php`** — реализация подготовки окружения тестов
   (автозагрузка + база `app_test` + миграции). Вызывает `bin/console` через
   `proc_open`, чтобы не тянуть `symfony/process` (в `vendor/` её нет, добавлять
   dev-only зависимость не хотелось); шаги идемпотентны.
4. **`.env.test`** — только `APP_ENV=test` (тестовая URL задана параметром в
   doctrine.yaml, см. п. 2).
5. **`fixtures/{offpeak_prices.json, offpeak_usage.jsonl, reprice.json}`** —
   фикстуры только для тестов (off-peak через полночь + дедупликация
   + ретро-коррекция); исходные `fixtures/prices.json`/`fixtures/usage.jsonl`
   не тронуты.
6. **`migrations/Version20261027000001.php`** — единственная миграция, создаёт
   все 4 таблицы (`providers`, `models`, `price_rows`, `usage_events`) и
   индексы: unique `(provider_code, code)` на models, unique
   `(provider_code, model_code, valid_from)` на price_rows (покрывает и lookup
   «последняя действующая строка» и дедупликацию), unique
   `(provider_code, provider_request_id)` на usage_events (дедупликация по
   TASK.md) и составной `(status, ts)` для выборки unrated-событий при расчёте.

`bin/console`, `config/services.yaml`, `bundles.php` — без изменений
(команды регистрируются автоматически: autowire + autoconfigure из
`config/services.yaml` + PSR-4 автозагрузка).
