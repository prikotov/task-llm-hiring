# PR: учёт расходов на LLM-токены (тарификация событий по истории цен)

## Что сделано

Консольное приложение на PHP (Symfony Console, Doctrine ORM, PostgreSQL)
учитывает расходы на LLM-токены: принимает историю цен (JSON) и журнал
событий использования (JSONL), тарифицирует события ценой, действовавшей в
момент запроса, и формирует CSV-отчёт. Все пять команд из задания работают
через `bin/console`:

| Команда | Назначение | Вывод |
|---|---|---|
| `app:prices:import <file>` | идемпотентная загрузка провайдеров/моделей/цен | сводка в stderr; rc 0 |
| `app:usage:import <file>` | приём JSONL-событий, дедупликация по `(provider, provider_request_id)` | сводка в stderr; rc 0 (даже если все строки — дубли) |
| `app:billing:rate` | тарификация нерассчитанных событий (повторные попытки для unrated) | stdout: `RATED=<n> UNRATED=<m>`; для каждого unrated — строка в stderr: `UNRATED <provider>/<request_id> <ts> no_price` |
| `app:billing:report [--from --to]` | CSV-отчёт по (user, model) + строка `TOTAL` | строго в stdout |
| `app:billing:reprice <file>` | **бонус:** ретро-коррекция строки цены + пересчёт событий именно по ней | stdout ровно две строки: `REPRICED_EVENTS=<n>` / `REPRICE_DELTA_USD=<x.xxxxxx>` |

Реализованы все тонкости из «Правил тарификации»:
- выбор строки цен по `valid_from ≤ ts` (включительно) — максимальная по
  дате среди доступных (смена цены «с этого момента» применяется ровно с
  этого момента — проверено событием e2 в примере);
- схемы `per_1m_tokens` (сумма по четырём видам токенов) и
  `per_1m_tokens_unified`;
- оф-пик на уровне провайдера: окно `[start, end)` по UTC, переход через
  полночь (`16:30–00:30`), множитель — на итог всего события;
- дедупликация приёма: повторная доставка игнорируется целиком, без
  обновлений и доначислений; одинаковый `provider_request_id` у разных
  провайдеров — разные события;
- unrated-события — штатное состояние (не ошибка), не попадают в отчёт,
  пере-тарифицируются при следующем `rate`, когда цены «подтянутся»;
- точные деньги: только bcmath, float не используется нигде на пути
  стоимости; единственное округление — HALF_UP до 6 знаков при расчёте
  события; суммы отчёта — бц-сумма уже округлённых стоимостей;
- период отчёта `--from` включительно, `--to` исключительно.

## Как устроено (архитектура)

Слои: `src/Domain` (чистая предметная модель и расчётные правила,
без фреймворка), `src/Application` (оркестрация и работа с БД),
`src/Command` (только консольный интерфейс), `src/Shared` (утилиты
валидации вводимых данных).

- **Domain.Catalog**: `Provider` (код, имя, оф-пиковое окно),
  `Model` (привязка к провайдеру), `PriceRow` (valid_from, валюта, схема
  `PricingUnit`, ставки-строки), `OffPeak` (окно + множитель),
  `PricingUnit` (enum схем с набором ключей ставок).
- **Domain.Usage**: `UsageEvent` — журнал; хранит коды провайдера/модели
  строками (без FK) — справочник цен может догонять журнал позже; ссылка
  `price_row_id` фиксирует строку цены, по которой событие рассчитано
  (именно она делает возможной reprice), `cost_usd decimal(38,6)`,
  статусы `pending → rated | unrated`.
- **Domain.Billing**: `CostCalculator` — порядок вычислений: точная сумма
  по видам → опциональный оф-пиковый множитель → единственное округление
  HALF_UP до 6 знаков; `DecimalMath` — HALF_UP, точность, деление на 1M.
- **Application**: `CatalogFileLoader`/`CatalogImporter` (валидация
  построчно, идемпотентный upsert «в файле или в БД»), `UsageFile`
  (полная проверка JSONL до записи — некорректная строка отменяет весь
  файл, частичной записи нет), `UsageImporter` (ONE-SHOT in transaction,
  `ON CONFLICT ... DO NOTHING`, подсчёт принятых/дубликатов),
  `RateService`, `ReportService`, `RepriceService`.
- **Схема БД**: одна миграция `migrations/Version20261017120000.php`
  (таблицы providers/models/price_rows/usage_events, уникальные ключи на
  естественные идентификаторы, FK с адекватным ON DELETE),
  `doctrine:schema:validate` — «in sync», на чистой базе
  `bin/console doctrine:migrations:migrate -n` разворачивает всё.

## Ключевые решения и допущения

1. **Расчёт стоимости события — единым «атомарным» шагом в тарификаторе**
   (`CostCalculator`): точные промежуточные значения (bcmath, масштаб 16),
   затем единственное округление HALF_UP до 6 знаков. Отчётные суммы не
   округляются повторно — бц-сложение уже округлённых значений.
2. **Дедупликация на уровне СУБД** (уникальное ограничение на
   `(provider_code, provider_request_id)` + `ON CONFLICT DO NOTHING`):
   корректно даже при параллельных импортах; «дубль» — это полное
   игнорирование (ни обновления, ни доначислений).
3. **Unrated — нормальный статус**, а не «ошибка тарификации»: событие
   остаётся в журнале, в отчёт не попадает; `app:billing:rate` каждый раз
   перебирает `pending + unrated`, поэтому «дошедшие позже» цены
   автоматически подхватывают события.
4. **Reprice (бонус)**: строка коррекции upsert-ится по
   `(provider, model, valid_from)`; провайдер/модель должны существовать
   в БД (секции `providers`/`models` в файле коррекции игнорируются —
   по заданию они могут отсутствовать). Пересчитываются **только**
   события, хранящие ссылку на изменённую строку; дельта = Σ(new − old);
   после reprice `app:billing:report` показывает пересчитанные суммы без
   задвоений (проверено: 14.262499 → 19.724998, delta 5.462499).
5. **Деньги только строками**: ставки, множитель, стоимость — десятичные
   строки; `float`/`double` в пути стоимости не встречаются (валидация
   входа отклоняет числа JSON для ставок: требуется строка).
6. **Время** — ISO-8601 с суффиксом `Z` (UTC); `--from` включительно,
   `--to` исключительно; выбор строки цен `valid_from ≤ ts` (включительно).
7. **Оф-пик** — окно на уровне **провайдера**, действует для всех его
   моделей; `off_peak: null` — скидки нет (в коде проверяется явно).

## Как проверяли

Всё — в этом репозитории, PostgreSQL на `127.0.0.1:5544` (БД `app`):

```
# схема на чистой базе
psql ... -c "DROP DATABASE IF EXISTS app;" -c "CREATE DATABASE app;"
bin/console doctrine:migrations:migrate -n
bin/console doctrine:schema:validate        # [OK] in sync

# полная приёмка из TASK.md (шаги 3–5)
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import  fixtures/usage.jsonl
bin/console app:billing:rate                                 # RATED=3 UNRATED=0
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
# -> совпадает с примером TASK.md байт-в-байт (проверено `diff`)
# повторный импорт + rate + report -> результат не меняется (проверено)

# unrated-поток
#  (событие на модель без цен -> UNRATED=1, в отчёте нет;
#   затем импорт цен этой модели -> rate -> RATED=1, попадает в отчёт)

# reprice (бонус)
bin/console app:billing:reprice /tmp/correct.json
# -> REPRICED_EVENTS=1, REPRICE_DELTA_USD=5.462499; отчёт отражает 19.724998

# unit-тесты
vendor/bin/phpunit          # OK (20 tests, 46 assertions)

# негативные пути (rc=1, сообщение в stderr)
echo '{broken' > /tmp/bad.json
bin/console app:prices:import /tmp/bad.json
```

Проверено вручную: выбор строки цены на границе «смена цены в 12:00»
(11:00 → старая, 12:00 → новая), оф-пиковая окно через полночь
(16:29 нет / 16:30 да / 23:59 да / 00:00 да / 00:30 нет), идемпотентность
повторных импортов, дедупликация, unrated → rated после догрузки цен.

## Отступления от каркаса / изменённые файлы

**Зависимости не добавлялись:** в `require` composer.json пакетов не добавлял — используются только уже присутствующие (Symfony, Doctrine, PHPUnit). `.gitignore`, `Makefile`, `docker-compose.yml`, `fixtures/`,
`phpunit.xml.dist`, `README.md` не трогались.

Новые (все — добавлены в ходе работ):

- `migrations/Version20261017120000.php` — единственная миграция схемы.
- `src/Shared/`: `ConsoleStreams.php`, `Decimals.php`, `InputFormatException.php`,
  `IsoUtc.php`, `JsonFile.php`.
- `src/Domain/Billing/`: `CostCalculator.php`, `DecimalMath.php`.
- `src/Domain/Catalog/`: `PricingUnit.php`, `OffPeak.php`, `Provider.php`,
  `Model.php`, `PriceRow.php`.
- `src/Domain/Usage/`: `EventStatus.php`, `UsageEvent.php`.
- `src/Application/Catalog/`: `ProviderSpec.php`, `ModelSpec.php`, `PriceSpec.php`,
  `CatalogFile.php`, `CatalogFileLoader.php`, `CatalogImportSummary.php`,
  `CatalogImporter.php`.
- `src/Application/Usage/`: `UsageFile.php`, `UsageImportSummary.php`, `UsageImporter.php`.
- `src/Application/Billing/`: `RateSummary.php`, `RateService.php`,
  `ReportService.php`, `RepriceSummary.php`, `RepriceService.php`.
- `src/Command/`: `ImportPricesCommand.php`, `ImportUsageCommand.php`,
  `BillingRateCommand.php`, `BillingReportCommand.php`, `BillingRepriceCommand.php`.
- `tests/`: `DecimalMathTest.php`, `CostCalculatorTest.php`, `OffPeakTest.php`,
  `InputValidationTest.php`.

**Альтернативные решения (если что-то считали иначе):**
- Реализовано в новом дереве `src/` (Domain/Application/Command/Shared)
  вместо того, чтобы менять старые файлы каркаса, — каркасные файлы не
  затронуты.
- Ретро-коррекция (reprice) сделана через **перезапись стоимости** события
  по его ссылке на строку цены + дельту; не используются корректирующие
  записи/журнал — по заданию это «на ваше усмотрение», а итоговый отчёт
  после reprice обязан быть без задвоений (проверено).
- Способ управления схемой = Doctrine Migrations (`make migrate`):
  на чистой базе `bin/console doctrine:migrations:migrate -n` разворачивает
  всю схему. `Makefile` оставлен типовым.
