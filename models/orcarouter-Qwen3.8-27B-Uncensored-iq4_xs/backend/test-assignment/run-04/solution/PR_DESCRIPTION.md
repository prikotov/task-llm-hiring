# Учёт расходов на LLM-токены

## Что сделано

Консольное приложение (PHP 8.4+, Symfony, Doctrine ORM, PostgreSQL 17),
которое принимает два потока данных — справочник цен (JSON) и события
использования (JSONL) — и считает расходы в разрезе пользователей и
моделей. Все команды ТЗ реализованы, включая бонусную ретро-коррекцию
цены.

### Команды

| Команда | Назначение |
|---|---|
| `bin/console app:prices:import <file>` | Идемпотентный upsert провайдеров (по `code`), моделей (по `(provider, code)`) и строк цен (по `(provider, model, valid_from)`) |
| `bin/console app:usage:import <file>` | Приём событий; дедупликация по `(provider, provider_request_id)` — дубли игнорируются, код выхода 0 |
| `bin/console app:billing:rate` | Тарификация нерассчитанных событий + повторные попытки `unrated`; `RATED=<n> UNRATED=<m>` в stdout, по строке `UNRATED <provider>/<id> <ts> no_price` в stderr на каждое |
| `bin/console app:billing:report [--from=] [--to=]` | CSV по парам `(user, <provider>/<model>)`, период `[from, to)`, TOTAL-строка; unrated не попадают |
| `bin/console app:billing:reprice <file>` | **Бонус.** Upsert исправленных строк цен (провайдер/модель должны существовать в базе), пересчёт затронутых событий, `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>` (новая − старая) |

Рабочий пример из ТЗ воспроизводится байт-в-байт (проверено; вывод
см. «Как проверяли»).

## Архитектура

Три слоя, зависимость только «вниз»:

```
src/
├── Console/Command/        — 5 команд Symfony Console (тонкие обёртки)
├── Application/            — use-case'ы:
│   ├── Catalog/            — CatalogImporter (+ CatalogInput: разбор/валидация JSON)
│   ├── Usage/              — UsageEventImporter (JSONL, дедупликация)
│   └── Billing/            — EventRater, BillingReportBuilder, Repricer + DTO результатов
├── Domain/                 — без Doctrine (кроме маппинг-атрибутов сущностей):
│   ├── Money/Money.php     — точные деньги на bcmath
│   ├── Catalog/            — Provider(+OffPeakWindow), Model, PriceRow, PricingUnit
│   └── Usage/              — UsageEvent, BillingStatus, TokenKind, EventPricingCalculator
├── Exception/              — DataImportException (ошибка входа, код 1)
└── Infrastructure/         — репозитории (PriceRowRepository, UsageEventRepository)
```

- `EventPricingCalculator` — чистая функция `(событие, строка цены,
  оф-пик) → Money`: формулы обеих схем тарификации, множитель оф-пика,
  единственное округление. Тестируется без БД.
- `Money` — финальный immutable-класс: значение — десятичная строка
  (масштаб 24), операции `add/subtract/multipliedBy/dividedBy` точные
  (некруглые) через bcmath; `roundedHalfUpToFixed()` — единственное
  допустимое округление (до 6 знаков, HALF_UP в сторону, отдалённую от
  нуля: 2.5 → 3). `float` в деньгах не используется.
- Событие тарифицируется строкой с наибольшим `valid_from ≤ ts`
  (DQL, JOIN через модель); `unrated` — законное состояние, повторные
  попытки при каждом `billing:rate`.
- Репозиторий ценов `findEffectiveAt` + уникальный индекс
  `(model_id, valid_from)`; события — уникальный индекс
  `(provider, provider_request_id)` (дедупликация и на уровне БД) и
  индекс по `ts` для периодов отчёта.

## Ключевые решения и допущения

1. **Схема БД — Doctrine Migrations.** Развертывание на чистой базе:
   `make db-up && make migrate` (стандартный flow каркаса). Миграция
   генерировалась `doctrine:migrations:diff` и сверялась с маппингом
   (`doctrine:schema:validate` — in sync), чтобы исключить рассинхрон
   hand-written схемы.
2. **Хранение денег.** `cost_usd` — `DECIMAL(30,6)`, читается/пишется
   строкой; суммирование в отчёте — сумма уже округлённых стоимостей
   событий, без повторного округления (проверено: 14.262499 + 8.400000
   + 11.000000 = 33.662499 «в лоб» bcmath).
3. **Оф-пик.** Окно `[window_start, window_end)` минут UTC; переход
   через полночь (16:30–00:30) обрабатывается как
   `[start, 24:00) ∪ [00:00, end)`. **Допущение:** окно с
   `start == end` считается пустым (без скидки) — трактовка «весь день»
   выглядела бы противоречивее; множитель обязателен положительным.
4. **Merging строк цен и idempotent upsert.** При повторном импорте
   та же строка не «пересоздаётся»: ищется по ключу, параметры
   перезаписываются `apply()` — сущность не сменяется, flush не создаёт
   новых записей. Для reprice то же: замена по
   `(provider, model, valid_from)`, провайдер и модель берутся из базы
   (секции `providers`/`models` в файле коррекции игнорируются).
5. **Реprice: перезапись без журнала.** Пересчёт затрагивает события
   моделей из файла коррекции: для каждого вычисляется новая **действующая**
   строка (она может отличаться от «старой», если корригируемая строка
   стала действующей на момент `ts` события) и новая стоимость по той же
   формуле; событие перезаписывается, только если изменилась строка или
   стоимость — повторная коррекция без изменений ничего не трогает
   (`REPRICED_EVENTS=0`, дельта 0). Итоговый отчёт не дублирует и не
   доначисляет: стоимость события — одна строка в `usage_events`.
   Дельта считается как сумма (новая − старая) по пересчитанным
   событиям, 6 знаков.
6. **Валидация входа** — строгая: неверный JSON / JSONL-строка,
   неизвестные виды токенов, нецелые/отрицательные счётчики, «короткие»
   ISO-время, не-USD — `DataImportException`, код 1, сообщение в stderr;
   события с `valid_from` раньше всех строк цены — `unrated` (не
   ошибка).
7. **stdout/stderr.** Отчёт — только в stdout (CSV, `\n`, UTF-8, без
   BOM); все служебные сообщения (ошибки, UNRATED-строки) — в stderr.
8. **Округление HALF_UP** реализовано в `Money` по единицу:
   `round(x) = floor(x + 0.5)` для неотрицательных (в т.ч. 2.5 → 3),
   симметрично — в сторону, отдалённую от нуля, для отрицательных
   (встречаются только в дельте reprice; суммы событий не
   пересчитываются с округлением).

## Добавленные файлы

- `src/...` (30 файлов, структура выше), `tests/...` (4 файла),
  `migrations/Version20261020140359.php`, `COMMIT_MESSAGE.md`.

**Зависимости не добавлялись** (`composer.json`/`composer.lock` не
менялись); файлов каркаса (`TASK.md`, `AGENTS.md`, `README.md`,
`fixtures/`, `Makefile`, `docker-compose.yml`, `phpunit.xml.dist`) —
тоже не менялось.

## Тесты

`vendor/bin/phpunit` — 28 тестов, 55 ассершенов.

- `MoneyTest` — точность операций, HALF_UP (включая 2.5→3 и отрицательные),
  6-знаковые строки, ошибки на не-десятичных значениях;
- `EventPricingCalculatorTest` — оба расчётных примера из ТЗ
  (14.262499, 8.400000), off-peak (в окне/вне, граница окна, двойное
  округление — «одинаковое округление на уровне события»), нулевые
  токены → 0.000000;
- `OffPeakWindowTest` — границы `[start, end)` (включительно/исключительно),
  переход через полночь, пустое окно, валидация;
- `BillingReportBuilderTest` — группировка/сортировка по `(user, model)`,
  TOTAL, суммирование без повторного округления, период `[from, to)`,
  unrated не попадают.

Тесты — чистые юнит-тесты домена/приложения (без БД и без Symfony DI),
поэтому `make test` работает независимо от миграций.

## Отступления от каркаса / альтернативные решения

1. **Имя колонки `user_name`.** В PostgreSQL `user` — зарезервированное
   слово; объявление колонки `user` требует кавычек во всех запросах
   (DQL/SQL). Чтобы не тащить кавычки и риски, колонка названа
   `user_name` (в JSON входного события — `user`, как в ТЗ; в CSV
   отчёта — `user`, как в ТЗ). Смысл не меняется.
2. **Схема через `migrations:diff`.** Каркас не предписывал способ
   управления схемой; выбран штатный `doctrine:migrations`, `make migrate`
   (из Makefile каркаса) — единственная точка развёртывания на чистой
   базе.
3. **Reprice: один путь пересчёта на «действующей» строке.** Мог быть
   выбран и вариант «только события, чья записанная `price_row` заменена»,
   но он недостоверен: если исправленная строка с меньшим `valid_from`
   смещает «действующую» строку события, её стоимость тоже изменится.
   Выбран принцип «пересчитать по новой действующей строке и
   перезаписать, если что-то изменилось» — устойчив к повторным
   коррекциям и не порождает задвоений.
4. **Не сделан (как и по ТЗ)** журнал корректировок/компенсирующие
   записи — механизм reprice на усмотрение, перезапись достаточна для
   требований (нет задвоений в отчёте; пользовательские суммы стабильны
   до самого reprice).

## Как проверяли

```bash
make db-up
psql -d postgres -c 'DROP DATABASE IF EXISTS app;'   # полностью чистая база
make migrate

# приёмка 1–4
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# → RATED=3 UNRATED=0
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# → точно:
#   user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
#   u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
#   u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
#   u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
#   TOTAL,,6200000,2300000,2500000,83333,33.662499

# приёмка 5 — идемпотентность: повторный импорт + rate + report, результат не меняется
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
# → RATED=0 UNRATED=0, отчёт идентичен

# unrated: событие раньше всех строк цены
echo '{"provider":"prism","provider_request_id":"e-early","model":"lumen-8","user":"u-carol","ts":"2026-07-01T10:00:00Z","tokens_input":100,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":0}' > /tmp/early.jsonl
bin/console app:usage:import /tmp/early.jsonl && bin/console app:billing:rate
# → stdout: RATED=0 UNRATED=1; stderr: UNRATED prism/e-early 2026-07-01T10:00:00Z no_price

# off-peak: событие в окне 16:30–00:30 (lumen-mini, input 1M × 0.4 × 0.5 = 0.2)
echo '{"provider":"prism","provider_request_id":"e-offpeak","model":"lumen-mini","user":"u-alice","ts":"2026-09-16T17:00:00Z","tokens_input":1000000,"tokens_output":0,"tokens_cache_read":0,"tokens_cache_write":0}' > /tmp/offpeak.jsonl
bin/console app:usage:import /tmp/offpeak.jsonl && bin/console app:billing:rate
# → в отчёте строка u-alice,prism/lumen-mini cost 0.200000

# reprice: правим input lumen-8 c 3.0 → 2.0 (строка с 2026-08-01)
echo '{"providers":[],"models":[],"prices":[{"provider":"prism","model":"lumen-8","valid_from":"2026-08-01T00:00:00Z","currency":"USD","unit":"per_1m_tokens","rates":{"input":"2.000000","output":"12.000000","cache_read":"0.300000","cache_write":"3.750000"}}]}' > /tmp/reprice.json
bin/console app:billing:reprice /tmp/reprice.json
# → REPRICED_EVENTS=1
# → REPRICE_DELTA_USD=-1.200000   (14.262499 → 13.062499; e2 за 12:00 не тронут — действует строка с 12:00)
# повторный reprice тем же файлом → REPRICED_EVENTS=0, дельта 0.000000

# ошибки: неверный JSON/JSONL/неизвестная модель — код 1, текст в stderr
# тесты
vendor/bin/phpunit
# → OK (28 тестов, 55 ассершенов)
```

