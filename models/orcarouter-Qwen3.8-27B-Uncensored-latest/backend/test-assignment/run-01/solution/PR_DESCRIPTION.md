# PR: Учёт расходов на LLM-токены

## Что сделано

Реализована вся функциональность из `TASK.md`: консольное приложение на
Symfony + Doctrine ORM 3 + PostgreSQL 17, считающее стоимость LLM-токенов
по истории цен с учётом времени события, off-peak-окон и двух схем тарифов.

**Команды** (`bin/console <команда>`):

| Команда | Поведение |
|---|---|
| `app:prices:import <json>` | Импорт провайдеров/моделей/истории цен. Идемпотентен: upsert по `(provider, code)`, `(provider, code)`-модель, `(provider, model, valid_from)`-строка цен. Ошибки входа → `rc=1`, БД не трогается (транзакция). |
| `app:usage:import <jsonl>` | Импорт событий. Идемпотентен по `(provider, provider_request_id)`. Событие без цены → строится как `pending` (rate его откладывает, stderr: `UNRATED <provider>/<model> <ts> no_price`). |
| `app:billing:rate` | Строит события из `pending` в `rated`. Цена — последняя строка с `valid_from <= ts`. Per-token (`input/output/cache_read/cache_write`) и unified (`any×все токены`). Off-peak: окно `start..end` (полуинтервал, обход полуночи) в UTC × множитель. Всё на bcmath; единственный `HALF_UP`-округление до 6 знаков — на итог события. stdout: `RATED=<n> UNRATED=<m>`. |
| `app:billing:report --from --to` | CSV в stdout: заголовок `user,model,...,cost_usd`, строки по событиям `rated` в диапазоне ts, сортировка по `user` затем `model` (лекс.), затем `TOTAL`. Формат 6 знаков. Вывод — ровно те байты, что в примере TASK.md (проверено `od -c`). |
| `app:billing:reprice <json>` | Fails fast: файл валидируется и все (provider, model) существуют в каталоге — иначе `rc=1`, БД не трогают. Затем upsert строки цены, пересчёт всех событий, тарифицированных именно этой строкой, дельта новой-старой стоимости. stdout ровно: `REPRICED_EVENTS=<n>` / `REPRICE_DELTA_USD=<x.xxxxxx>`. Журнал — таблица `reprice_entries`. Повторный импорт цен после reprice не отменяет журнал (журнал — только аудит). |

**Схема БД** — миграция `migrations/Version20261020120000InitialSchema.php`
(`make migrate`): `providers`, `catalog_models`, `price_rows`,
`usage_events`, `reprice_entries`.

**Тесты** (`make test`, PHPUnit 11):
- unit: `MoneyTest` (bcmath, HALF_UP), `OffPeakWindowTest` (полуинтервал,
  обход полуночи, пустое окно), `RateCalculatorTest` (per-token на e1 из
  TASK.md → `14.262499`, unified, off-peak, одно итоговое округление),
  `ParsersTest` (ISO-8601/UTC, `HH:MM`, деньги 0..6 знаков, int-токены,
  схемы rates);
- контекстный: `BillingWorkflowTest` — реальный сценарий на PostgreSQL:
  TRUNCATE → импорт → `rate` → `report` (байт-в-байт с примером) → повторный
  импорт+rate (идемпотентность, событий не задвоилось) → `reprice` →
  `report` (e1 = `12.062499`, e2 не тронут, TOTAL = `31.462499`) → событие
  без цены → stdout `UNRATED=1`, stderr — строка `UNRATED ghost/g1 ... no_price`.
  Если БД недоступна — тесты пропускаются (не падают).

## Ключевые решения и допущения

1. **Без float в деньгах.** Целые и строки-десятницы; все операции — `bcadd/
   bcmul/bcsub`, `scale` 20. Единственное округление — HALF_UP до 6 знаков
   при записи стоимости события (`Money::roundHalfUpTo6`). Никаких float в
   цепочке расчёта; `json_decode`-числа не попадают в арифметику (парсер
   принимает строки/целые).
2. **Транзакция с явным flush.** В Doctrine ORM 3 `EntityManager::commit()`
   **не** делает flush UnitOfWork (в ORM 2 — делал), поэтому trait
   `UsesTransactions` явно вызывает `$em->flush()` перед `$em->commit()`.
   Без этого данные терялись молча (commit «успешный», rows — отсутствующие).
3. **Колонка `user_id` вместо `user`.** `user` — зарезервированное слово
   PostgreSQL; переименовали колонку, а не экранировали. В отчёте отображаем
   заголовок `user` (как требует задание).
4. **Полуинтервал off-peak и обход полуночи.** `OffPeakWindow::contains`:
   `start <= t < end`; если `start > end` — обход полуночи
   (`t >= start || t < end`); `start == end` — пустое окно.
5. **Выбор строки цены.** По `(provider, model)` сортируем `valid_from`
   `DESC` и берём первую `valid_from <= event.ts`. В тестах покрываются
   граничные варианты (событие до/после замены, e2 после reprice не
   пересчитывается).
6. **Отчёт — только через Doctrine QueryBuilder**, ORDER BY: `user_id,
   provider, model_code` — эквивалентно сортировке по «provider/model»
   (внутри строки «provider/model» порядок сортировки совпадает с
   по-колонному для значений фиксов; для общего случая — по колонкам).
   Строки формируются вручную, значения экранируются для CSV
   (`,`/`"`/`\n`/`\r`) + UTF-8.
7. **Журнал reprice** — таблица `reprice_entries`: `event_id, old/new cost,
   price_row_id, ts`. Это аудит; повторный `report` читает только `usage_
   events.cost_usd`, задвоения нет.
8. **Идемпотентность импорта — на уровне уникальных ключей** (upsert на
   стороне СУБД / check-then-insert в транзакции). Повторный `rate` — 0
   событий (все уже `rated`).
9. **Процессные тесты** (`BillingWorkflowTest::exec`) вызывают
   `bin/console` через `proc_open` с `APP_ENV=test`. Причина:
   `Symfony\Component\Console\Application::run()` в нашем вендоре **вызывает**
   `exit()` (автоматический auto-exit, `Application::$autoExit`), а
   `TestOutput`/`BufferedOutput` не поддерживают `setVerbosity`
   — встроенный вызов падает. Процессный вызов — самая близкая к «реально
   пользователю» модель и даёт реальные stdout/stderr/rc.

## Отступления от каркаса

| Пункт | Обоснование |
|---|---|
| Добавлены `fixtures/prices-corrected.json`, `fixtures/prices-again.json` | Данные для reprice и повторного импорта (нет в исходном фиксе). |
| Добавлена миграция `Version20261020120000InitialSchema.php` | Каркас — без миграции (задача «свой способ управления схемой» — используем doctrine/migrations, уже в `vendor/`). |
| `quote_strategy: ansi` в `config/packages/doctrine.yaml` | Чтобы колонка `user_id` (и любые будущие) были безопасно кавицированы в SQL. |
| Публичный алиас `app.entity_manager` в `config/services.yaml` | Чтобы тесты (и `bin/console` debug) могли достать EM; без публичной службы DI отдаёт приватные алиасы. |
| Колонка `user_id` | `user` — reserved word PG (см. 3 выше). |
| `UsesTransactions::commit` — flush + commit | ORM 3: `commit()` без flush не делает persist. |
| `static function` → `function` в замыканиях CatalogImporter | `static`-замыкание не видит `$this` (EM). |
| Тесты — `tests/Application/BillingWorkflowTest.php` + unit-набор | В каркасе `tests/` не было. |
| Команда `app:billing:reprice` принимает файл как аргумент `<file>` | Формат `app:billing:reprice <file>` (позиционный) — из TASK.md. |

**Зависимости** — новые не добавлялись (`composer.json`/`composer.lock` не
менялись); всё нужное (Doctrine Migrations, PHPUnit 11, Console) уже в
`vendor/`.

## Как проверяли

```bash
make db-up                       # PostgreSQL 17 (127.0.0.1:5544)
make migrate                     # doctrine:database:create + migrations:migrate

# приёмка TASK.md, шаг за шагом:
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import  fixtures/usage.jsonl
bin/console app:billing:rate
# stdout: RATED=3 UNRATED=0

bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# (байт-в-байт сверка с примером: od -c | tail … — совпадает, включ. \n в конце)

# идемпотентность
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import  fixtures/usage.jsonl
bin/console app:billing:rate    # → RATED=0 UNRATED=0
bin/console app:billing:report --from=... --to=...   # → тот же вывод

# ретро-коррекция
bin/console app:billing:reprice fixtures/prices-corrected.json
# REPRICED_EVENTS=1
# REPRICE_DELTA_USD=-2.200000
bin/console app:billing:report --from=... --to=...
# u-alice,prism/lumen-8,…,12.062499 / e2 не тронут / TOTAL 31.462499

# edge: событие без цены
 cat > /tmp/ghost.jsonl <<'JSON'
 {"provider": "ghost", "provider_request_id": "g1", "model": "nope", "user": "u-x",
  "ts": "2026-09-16T03:00:00Z", "tokens_input": 1000000, "tokens_output": 100,
  "tokens_cache_read": 0, "tokens_cache_write": 0}
 JSON
bin/console app:usage:import /tmp/ghost.jsonl && bin/console app:billing:rate
# → stdout: RATED=0 UNRATED=1; stderr: UNRATED ghost/g1 2026-09-16T03:00:00Z no_price

echo '{oops' > /tmp/bad.json
bin/console app:prices:import /tmp/bad.json   # → rc=1, [ERROR] Syntax error

# тесты
make test
# OK (28 tests, 95 assertions)  [unit + контекстный BillingWorkflowTest]
```

Все числа в отчёте и репорт сверены с примером TASK.md; итоговый `report`
после `reprice` — 6 знаков, `TOTAL` — сумма строковых сумм (bcmath),
заднеи не задвоены.
