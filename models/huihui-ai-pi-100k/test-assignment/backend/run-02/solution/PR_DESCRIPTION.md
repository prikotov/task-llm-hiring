# PR: учёт расходов на LLM-токены

Реализован консольный бэкенд учёта расходов на LLM-токены по задачам из
`TASK.md`: импорт истории цен, импорт журнала использования, тарификация,
отчёт и ретро-коррекция цен (бонус). Выводы — machine-строки в `stdout`,
служебные сообщения — в `stderr`; exit-коды 0/1.

## Что сделано

Все команды — в `bin/console` (Symfony Console), хранение — PostgreSQL 17
(Doctrine ORM + DBAL), деньги — только `bcmath`.

| Команда | Назначение |
|---|---|
| `app:prices:import <file>` | Идемпотентная загрузка справочника: провайдеры (upsert по `code`), модели (по `(provider, code)`), строки цен (по `(model, valid_from)`). Все три раздела — upsert; повторная загрузка не изменяет данные. |
| `app:usage:import <file>` | Приём JSONL-журнала. Дубли по `(provider, provider_request_id)` игнорируются целиком (уникальный индекс в БД + проверка перед insert). «Код 0, даже если все строки — дубли»; битая строка — код 1. |
| `app:billing:rate` | Тарификация всех нерассчитанных событий с повторными попытками для unrated (цены могли прийти позже). `stdout`: ровно одна строка `RATED=<n> UNRATED=<m>`; `stderr` для каждого unrated: `UNRATED <provider>/<id> <ts> no_price`. |
| `app:billing:report [--from=… --to=…]` | Строгий CSV в `stdout` (заголовок, строки по `(user, provider/code)`, `TOTAL`), сортировка — лексикографическая (`C`-коллиация). Период: `--from` включительно, `--to` исключительно. Unrated-события в отчёт не попадают. |
| `app:billing:reprice <file>` (бонус) | Приложение файла коррекции (формат секции `prices`; секции `providers`/`models` допустимы, но не обязательны — модель должна существовать в БД). Затронутые строки upsert-ятся, рассчитанные по ним события пересчитываются на месте, каждая корректировка фиксируется в `reprice_log`. `stdout`: ровно две строки `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>`. |

## Архитектура

Слои в `src/`:

- `Domain/` — предметная логика, не зависящая от БД и Symfony:
  - `Money` — точные десятичные суммы (bcmath, масштаб 16; HALF_UP; ровно
    одно округление при записании стоимости события до 6 знаков по правилу
    из ТЗ — «2.5 → 3», т.е. half-up, реализовано целочисленными операциями);
  - `OffPeakWindow` — окно `[start; end)` по времени суток UTC, переход через
    полночь, вкл./искл. границы;
  - `PricingUnit` — `per_1m_tokens` / `per_1m_tokens_unified` (+ обязательные
    ключи ставок — `input/output/cache_read/cache_write` либо `any`);
  - `RateCalculator` — формулы ТЗ: `Σ tokens·rate / 1e6` (+off-peak), unified:
    `Σ tokens · any / 1e6`;
  - `TokenUsage`, `Iso8601`, `UsageEventStatus` — вспомогательные типы.
- `Import/` — парсеры и DTO файлов (`PriceFileParser`, `UsageFileParser`);
  валидация строгая: все нарушения формата — `ImportException` (код 1).
- `Entity/`, `Repository/` — persistence (Provider, Model, Price,
  UsageEvent, RepriceLog).
- `Service/` — оркестрация операций: `PricesImportService`,
  `UsageImportService`, `RatingService`, `ReportService`, `RepriceService`.
- `Command/` — тонкие консольные команды (аргументы, stdout/stderr, exit-коды).

## Ключевые решения и допущения

1. **Точность денег.** Везде bcmath с масштабом ≥ 16; float не участвует
   (`Money`). Округление одно: итог события — HALF_UP до 6 знаков; отчёт —
   сумма уже округлённых стоимостей (агреgat `SUM` в PostgreSQL над
   `NUMERIC(30,6)` точен — повторного округления слагаемых нет).
2. **Выбор цены события** — наибольшее `valid_from <= ts`
   (`valid_from` включительно), т.е. для 12:00:00 берётся строка с
   `valid_from 12:00`. Реализовано через запрос с `ORDER BY valid_from DESC`.
3. **Статус rated/unrated** выведен из данных: `cost IS NULL` — unrated;
   отдельной колонки `status` в схеме нет.
4. **Событие хранит «снимок»** значений провайдера/модели
   (`provider_code`, `model_code`), связь со справочником (сущностью
   `Price`) устанавливается при тарификации. События можно принимать до
   появления справочника — они остаются unrated и досчитываются при
   повторном запуске `app:billing:rate`.
5. **Отчёт группирует** по `(user, provider_code, model_code)` — это эквивалент
   `(user, model)` в формате `<provider>/<code>`, как требует ТЗ; unrated —
   вне отчёта, `TOTAL` — сумма по отчёту, `model` в `TOTAL` пуст.
6. **Ретро-коррекция** (бонус) реализована как «перезапись + журнал»:
   стоимость события перезаписывается новой, старая фиксируется в
   `reprice_log` (event_id, old_cost, new_cost). Дельта = новая сумма −
   старая сумма; повторное применение того же файла даёт дельту `0.000000`
   (идемпотентность по эффекту на отчёт). Механизм — «перезапись с журналом
   корректировок», один из вариантов, допустимых ТЗ.
7. **Схема БД** — через Doctrine-миграции (`make migrate` — типовая команда
   из каркаса, изменений в способе не потребовалось). Две миграции:
   - `Version20261016112958` — справочник (providers/models/prices) и
     `usage_events`; все объекты создаются идемпотентно
     (`IF NOT EXISTS` + guarded unique-индексы), т.к. версия совпадает с
     версией, уже зафиксированной в эталонной базе (повторное применение
     на подготовленной БД не должно ошибаться);
   - `Version20261016235959` — `reprice_log` (журнал ретро-коррекции).
8. **Время** во всех вычислениях приводится к UTC (`DateTimeImmutable`);
   сравнения границ отчёта и выбор цены — по UTC-значениям.
9. **Сортировка отчёта** — по `(user, provider/code)`, лексикографическая
   (сортировка `provider_code || '/' || model_code`), что соответствует
   «по user, затем по model (лексикографически)».
10. **`app:billing:reprice` принимает файл** и как позиционный аргумент,
    и как `--file` (для совместимости с разными инволюциями). Секции
    `providers`/`models` в файле коррекции не обязательны.
11. **Парсинг** — строгий: некорректный JSON/JSONL, неизвестная `unit`,
    битый `valid_from`/`ts`, не-десятичные `rates`, отрицательные токены —
    всё это ошибка (код 1) с диагностикой в `stderr`. `rates` должны быть
    JSON-строками (не числами), чтобы float не просочился в арифметику.
12. **Команды не используют интерактивные диалоги**, аутентификацию, UI —
    всё вне рамок ТЗ.

## Как проверяли

Повторяемые шаги (все выполнялись; вывод в `stdout`/`stderr` как в ТЗ):

```bash
# 0) окружение
make db-up
make migrate                                   # схема на чистой базе

# 1) импорт (идемпотентный)
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl

# 2) тарификация
bin/console app:billing:rate
#   -> stdout: RATED=3 UNRATED=0 ; exit 0

# 3) отчёт — байт-в-байт с примером из TASK.md
bin/console app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z
#   (сверка через cmp с эталоном — идентично)

# 4) повторный импорт + rate + report — результат не меняется
#   (created: 0 / дубли: 3 / RATED=0 / отчёт идентичен)

# 5) ретро-коррекция (файл, меняющий input-ставку lumen-8 с 2026-08-01
#    на 3.600000)
bin/console app:billing:reprice correction.json
#   -> stdout ровно:
#   REPRICED_EVENTS=1
#   REPRICE_DELTA_USD=0.720000
bin/console app:billing:report
#   -> u-alice,prism/lumen-8,...,14.982499 ; TOTAL ... 34.382499
#   повторный reprice: REPRICED_EVENTS=1, REPRICE_DELTA_USD=0.000000

# 6) unrated → rated: событие, принятое до появления цены, остаётся unrated
#    и досчитывается после импорта соответствующей строки цен.

# 7) ошибки: битый JSON/JSONL, битые --from/--to, отсутствие --file
#    -> exit 1, сообщение в stderr.

# 8) юнит-тесты
make test     # OK (55 tests, 117 assertions)
```

Покрытия тестами: `Money` (арифметика, HALF_UP, отказ от float),
`OffPeakWindow` (вкл./искл. границы, полночь, множитель), `TokenUsage`,
`PricingUnit`, `RateCalculator` (обе схемы, off-peak, округление).
Доменные сценарии (импорт/rate/report/reprice, unrated→rated, дубли,
окна периода, границы оф-пика) — проверены сквозными прогонами
с фикстурами из `fixtures/` и служебными файлами в `/tmp`.

## Отступления от каркаса

Каркасные файлы (`TASK.md`, `AGENTS.md`, `README.md`, `fixtures/`,
`composer.json`/`composer.lock`, `docker-compose.yml`, `Makefile`,
`phpunit.xml.dist`) **не изменялись**; новые зависимости **не добавлялись**
(используется только то, что уже было в `composer.json` и `vendor/`).
`.env` создан как копия предоставляемого `.env.example` (необходим для
`bin/console`; в каркас входит `.env.example`). `config/packages/` и
`config/*.yaml` каркасные (autowire, doctrine) — не трогал.

Добавленные файлы (новая кодовая поверхность):

- `src/Domain/` — `Money.php`, `OffPeakWindow.php`, `TokenUsage.php`,
  `PricingUnit.php`, `RateCalculator.php`, `Iso8601.php`, `UsageEventStatus.php`;
- `src/Import/` — DTO и парсеры (`ProviderDefinition`, `ModelDefinition`,
  `PriceRowDefinition`, `PriceFile`, `UsageEventInput`,
  `PriceFileParser`, `UsageFileParser`);
- `src/Entity/` — `Provider.php`, `Model.php`, `Price.php`, `UsageEvent.php`,
  `RepriceLog.php`;
- `src/Repository/` — `ProviderRepository.php`, `ModelRepository.php`,
  `PriceRepository.php`, `UsageEventRepository.php`;
- `src/Service/` — `PricesImportService.php`, `UsageImportService.php`,
  `RatingService.php`, `ReportService.php`, `ReportRow.php`, `RepriceService.php`;
- `src/Command/` — `PricesImportCommand.php`, `UsageImportCommand.php`,
  `BillingRateCommand.php`, `BillingReportCommand.php`,
  `BillingRepriceCommand.php`;
- `src/Exception/ImportException.php`;
- `migrations/Version20261016112958.php`,
  `migrations/Version20261016235959.php`;
- `tests/Domain/*` (`MoneyTest`, `OffPeakWindowTest`, `TokenUsageTest`,
  `PricingUnitTest`, `RateCalculatorTest`),
  `tests/Import/PriceFileParserTest.php`, `tests/Import/UsageFileParserTest.php`;
- `.env` (копия `.env.example`).

Альтернативные решения и их обоснование:

- **Механизм reprice**: «перезапись + журнал `reprice_log`» (а не
  компенсирующие записи) — проще, ТЗ прямо допускает этот вариант;
  журнал покрывает аудит.
- **Идемпотентный DDL в базовой миграции** (`IF NOT EXISTS` + guarded
  unique-индексы) — т.к. версия миграции совпадает с эталонной БД и на
  уже подготовленной базе не должна падать; на чистой базе создаёт полную
  схему.
- **`app:billing:reprice <file>` как позиционный аргумент + `--file`** —
  ТЗ показывает командную строку `<file>`; опция оставлена для
  единообразия с именованными параметрами остальных команд.
- **Событие хранит снимки provider/model**, а не FK на справочник —
  чтобы принимать события до появления справочника (unrated→rated).
- **Оф-пик и unit как строки** в БД (не float) — float не участвует
  в деньгах; множитель преобразуется в `Money` только при вычислении.
