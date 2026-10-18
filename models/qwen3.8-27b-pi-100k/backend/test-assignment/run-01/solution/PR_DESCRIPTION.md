# PR: Учёт расходов на LLM-токены (консольное приложение)

## Что сделано

Реализованы все команды задания плюс бонус (ретро-коррекция цены):

- **`app:prices:import <file>`** — идемпотентная загрузка справочника
  (провайдеры → модели → строки цен) из JSON. Upsert по естественным
  ключам (`code`; `(provider, code)`; `(provider, model, valid_from)`);
  строки с идентичным содержимым не переписываются. Некорректный файл —
  ошибка с кодом 1, транзакция не коммитится.
- **`app:usage:import <file>`** — загрузка событий (JSONL). Дубли по
  `(provider, provider_request_id)` игнорируются целиком (первая доставка —
  каноническая), повторный импорт идемпотентен, код 0. Любая некорректная
  строка — ошибка всего файла (код 1, ничего не записывается).
- **`app:billing:rate`** — тарификация всех ещё не рассчитанных событий
  (повторные попытки для unrated включаются автоматически). Выбор цены —
  строка модели с наибольшим `valid_from ≤ ts` (граница включительная,
  проверяется по UTC-времени). `stdout`: `RATED=<n> UNRATED=<m>`; для
  каждого оставшегося unrated — строка в stderr:
  `UNRATED <provider>/<request_id> <ts> no_price`.
- **`app:billing:report [--from=… --to=…]`** — CSV в stdout: заголовок
  строго по спецификации; по одной строке на пару (user, `provider/code`),
  отсортировано по `user`, затем по `model` (лексикографически, по
  возрастанию); `cost_usd` — сумма уже округлённых стоимостей событий,
  ровно 6 знаков; последняя строка `TOTAL` с пустым `model`; unrated не
  попадают. `--from` включительно, `--to` исключительно.
- **`app:billing:reprice <file>` (бонус)** — файл того же формата, секции
  `providers`/`models` могут отсутствовать; строки цен должны ссылаться на
  уже существующих в базе провайдера/модель. Применяется upsert по
  `(provider, model, valid_from)`, затем пересчитываются все события,
  тарифицированные затронутыми строками. В stdout ровно две строки:
  `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>` (новая сумма минус
  старая, 6 знаков). Аудит — в таблицу `reprice_corrections`
  (событие, строка цен, старая/новая стоимость). Итоговый отчёт показывает
  пересчитанные суммы без задвоений; повторное применение той же коррекции
  идемпотентно (дельта 0, новых записей аудита нет).

## Архитектура (слои)

```
src/
├── Command/    app:prices:import, app:usage:import, app:billing:rate,
│               app:billing:report, app:billing:reprice
├── Domain/     чистая предметная логика (без БД):
│               Money (bcmath-арифметика для денег), CostCalculator,
│               OffPeakWindow, IsoTime, RatingRunner, RepriceRunner,
│               ReportBuilder + DTO (UnratedEvent, RatingSummary,
│               RepriceResult, ReportRow)
├── Entity/     ORM-модель: Provider, Model, PriceRow, UsageEvent,
│               EventRating, RepriceCorrection
├── Repository/ Doctrine-репозитории
└── Import/     парсеры/валидация входных файлов и сервисы вставки
```

## Ключевые решения и допущения

1. **Деньги — только точные десятичные строки (bcmath).** Промежуточные
   значения ведутся с масштабом 30; округление ровно одно — стоимость
   события, HALF_UP до 6 знаков (проверено тестом на
   0.5000025 → 0.500003: HALF_EVEN дал бы 0.500002). Тип `float` для
   денег не используется нигде (ставки, множители, стоимости — `string`).
2. **Время — только UTC.** Входные `ts`/`valid_from` валидируются как
   строгие ISO-8601 с суффиксом `Z`; оф-пик и выбор строки цен — по
   минуте суток UTC. `timestamptz` в БД хранит мгновенное время; все
   сравнения в SQL — строками UTC, поведение не зависит от часового пояса
   сессии PostgreSQL.
3. **Оф-пик** — окно на уровне провайдера, `[window_start, window_end)`,
   может переходить полночь; множитель применяется ко всей стоимости
   события до финального округления. Окно задаётся в справочнике цен и
   действует при тарификации (зафиксировано в записи расчёта вместе с
   ценой строки).
4. **Модель расчёта «тарификация + история».** Стоимость события
   рассчитывается один раз и сохраняется в `event_ratings` (ссылка на
   строку цен). Отчёт — это выборка рассчитанных событий за период;
   коррекция цен (bонус) пересчитывает затронутые записи «на месте» с
   аудитом, поэтому дублей начислений в отчёте нет. Альтернатива
   (суммирование компенсирующих записей) отвергнута: усложняет отчёт и
   аудит без выигрыша.
5. **Unrated — ожидаемое состояние**, а не ошибка: событие не
   тарифицируется и вне отчёта; каждый цикл rate перепроверяет все
   нерассчитанные (цены могли появиться позже).
6. **Дедупликация событий** — проверка по естественному ключу
   `(provider_code, provider_request_id)` перед вставкой + уникальный
   индекс в БД как страховка; повторная доставка игнорируется целиком,
   «первое» событие каноническое.
7. **Естественные клавиши в ORM-метаданных** (уникальные ограничения).
   Миграция `migrations/Version20261017000000.php` — автогенерация из
   метаданных (`doctrine:migrations:diff`); `doctrine:schema:validate`
   подтверждает синхронность.
8. **Отчёт**: поля с «,», «"» или переводами строк экранируются по
   RFC 4180 (в заданных фикстурах таких значений нет); пустой отчёт —
   заголовок + `TOTAL` нулей; код 0.
9. **Ретро-коррекция**: секция `providers` файла коррекции может менять
   `name`/`off_peak`; *отсутствующее* поле `off_peak` не сбрасывает
   сохранённое окно (явный `null` — сбрасывает), чтобы файл только с
   `prices` не менял ничего, кроме цен.

## Отступления от каркаса / изменения

- **Изменённые файлы каркаса: отсутствуют.** `TASK.md`, `AGENTS.md`,
  `README.md`, `fixtures/`, `composer.json`/`composer.lock`,
  `docker-compose.yml`, `Makefile`, `phpunit.xml.dist`, `bin/console`,
  `config/` не менялись.
- **Добавленные зависимости: отсутствуют** (использованы только
  заявленные в `composer.json`: Symfony Console/Doctrine, `ext-bcmath`).
- **Новые файлы** (кратко):
  - `src/Domain/`: Money, CostCalculator, OffPeakWindow, IsoTime,
    RatingRunner, RepriceRunner, ReportBuilder, ReportRow, UnratedEvent,
    RatingSummary, RepriceResult;
  - `src/Entity/`: Provider, Model, PriceRow, UsageEvent, EventRating,
    RepriceCorrection;
  - `src/Repository/`: ProviderRepository, ModelRepository,
    PriceRowRepository, UsageEventRepository, EventRatingRepository;
  - `src/Import/`: PricesParser, UsageParser, PricesPayload, ProviderSpec,
    ModelSpec, PriceSpec, UsageEventLine, PricesImporter, UsageImporter,
    PriceCatalog, AbstractJsonParser, ImportException;
  - `src/Command/`: PricesImportCommand, UsageImportCommand,
    BillingRateCommand, BillingReportCommand, BillingRepriceCommand;
  - `migrations/Version20261017000000.php` (схема БД);
  - `tests/`: MoneyTest, CostCalculatorTest, OffPeakWindowTest,
    ReportBuilderTest, PricesParserTest, UsageParserTest (34 теста,
    72 assertions, `make test`).
- **Небольшое отклонение в словаре команд**: вывод импортов — русские
  строки-сводки (задание такого вывода не предписывает и не запрещает):
  `Импортировано: провайдеров — 2, моделей — 3, строк цен — 4.` и
  `Принято событий: 3, отклонено дублей: 0.` — формат предсказуем и
  машиночитаем, все обязательные форматы (`RATED=/UNRATED=…`,
  `REPRICED_EVENTS=…/REPRICE_DELTA_USD=…`, CSV отчёта) — точно по
  спецификации.

## Как проверяли (шаги)

```bash
# 1) Чистая база + схема (по умолчанию — make migrate)
make db-up                                   # docker compose (из каркаса)
PGPASSWORD=app psql -h 127.0.0.1 -p 5544 -U app -d app -c "DROP TABLE IF EXISTS event_ratings, models, price_rows, providers, usage_events, reprice_corrections, doctrine_migration_versions CASCADE;"
make migrate
bin/console doctrine:schema:validate         # [OK] … in sync …

# 2) Приёмка из TASK.md
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
#   → совпадает байт-в-байт с примером из TASK.md (подтверждено diff)
#   повторный импорт + rate + report → результат не меняется (подтверждено)

# 3) Бонус: ретро-коррекция (пример: input 3.0 → 3.2 для lumen-8 с 2026-08-01)
bin/console app:billing:reprice <file.json>
#   REPRICED_EVENTS=1 / REPRICE_DELTA_USD=0.240000; audit в reprice_corrections;
#   отчёт после — без задвоений; повторный прогон той же коррекции: дельта 0.

# 4) Юнит-тесты домена
make test                                    # OK (34 tests, 72 assertions)
```

Проверенные поведенческие граничные случаи (вручную + тестами):
выбор цены на границе `valid_from` включительно; оф-пик: начало окна
включительно, конец эксклюзивно, переход через полночь; округление
HALF_UP; unrated-события (цена появляется позже → в следующем цикле
rate рассчитаны); дубли событий с одинаковым `provider_request_id` у
разных провайдеров — отдельные события.
