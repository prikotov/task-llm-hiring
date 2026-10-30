# PR: Учёт расходов на LLM-токены (тарификация, приём, отчёт, ретро-коррекция)

## Что сделано

Реализовано консольное приложение учёта расходов на LLM-токены по TASK.md:

| Команда | Назначение |
|---------|------------|
| `bin/console app:prices:import <file.json>` | Идемпотентная загрузка провайдеров/моделей/истории цен |
| `bin/console app:usage:import <file.jsonl>` | Идемпотентная загрузка событий (дедуп по `(provider, provider_request_id)`) |
| `bin/console app:billing:rate` | Тарификация ещё не расценённых событий; на выходе `RATED=<n> UNRATED=<m>` |
| `bin/console app:billing:report [--from=ISO] [--to=ISO]` | CSV-отчёт по расценённым событиям |
| `bin/console app:billing:reprice <file.json>` | Бонус: ретро-коррекция строки цен с журналом в `reprice_log` |

Схема БД разворачивается миграцией `migrations/Version20261027000001.php`
(одна миграция, `doctrine:migrations:migrate`) на чистой базе PostgreSQL 17.

## Изменённые/добавленные файлы

Каркас (`TASK.md`, `AGENTS.md`, `README.md`, `fixtures/`, `composer.json`,
`composer.lock`, `docker-compose.yml`, `Makefile`, `phpunit.xml.dist`,
`bin/console`, `src/Kernel.php`, `config/` каркасные файлы, `vendor/`) — не
менялся. Изменено/добавлено только:

- `src/Domain/Billing/MoneyMath.php` — точная бcmath-арифметика:
  `add/sub/mul/div` (scale 16 — запас точности, float не используется) и
  `roundHalfUpTo6` — единственное округление (HALF_UP до 6 знаков).
  Реализация: `bcdiv(bcadd(bcmul(value, 1e6, 16), '0.5', 0), 1e6, 6)` —
  масштабируем за полным запасом, добавляем 0.5 микрона и обрезаем
  (для неотрицательных значений это точно HALF_UP).
- `src/Domain/Billing/PricingCalculator.php` — стоимость события:
  `per_1m_tokens` (по видам input/output/cache_read/cache_write) и
  `per_1m_tokens_unified` (единый `any`); множитель оф-пика применяется
  к итогу события **до** единственного округления.
- `src/Domain/Billing/OffPeak.php` — проверка попадания времени события
  в окно `[window_start, window_end)` UTC; учитывает переход через
  полночь (например `16:30–00:30`): условие `ts >= start || ts < end`,
  если `start > end`.
- `src/Domain/Billing/UsageRecord.php` — value-object строки JSONL (строки
  `provider`/`model`/`user`/`provider_request_id`, `ts: DateTimeImmutable`,
  четыре целых количества токенов).
- `src/Import/InputParser.php` — разбор и валидация входных файлов: JSON
  (цены) и JSONL (события); ошибки — `InputFormatException` с указанием
  строки/поля.
- `src/Entity/{Provider,Model,Price,UsageEvent,RepriceLogEntry}.php` — сущности ORM
  (Doctrine-атрибуты, PSR-4). Цены — строки (bcmath), токены — int.
- `src/Repository/{PriceRepository,UsageEventRepository}.php` — запросы по
  ключам (вкл. выбор «наибольшее valid_from ≤ ts» через DQL).
- `src/Service/PriceCatalogService.php` — upsert провайдеров/моделей/цен
  (повторная загрузка = перезапись значений; идемпотентность).
- `src/Service/UsageIngestService.php` — загрузка JSONL: вставка с
  `ON CONFLICT (provider_code, provider_request_id) DO NOTHING` —
  дедупликация по естественному ключу; повторная доставка события
  целиком игнорируется; unrated-события при импорте не трогаются (их
  пере-тарифицирует `app:billing:rate`).
- `src/Service/BillingService.php` — тарификация: выбор строки цены по
  `valid_from ≤ ts` (наибольшее), применение оф-пика, единственное
  округление HALF_UP, обновление события (`status='rated'`, `cost_usd`,
  `price_valid_from`); идемпотентность — уже расценённые не пересчитываются.
- `src/Service/ReportService.php` — CSV по расценённым событиям,
  группировка `(user, model)`, сортировка по `user`→`model` (лексикографическая),
  `TOTAL`-строка; `cost_usd` — сумма уже округлённых цен (не пересчитывается).
- `src/Command/*.php` — 5 консольных команд (`AsCommand`, exit-коды,
  stdout — данные/сводка, stderr — служебные сообщения и `UNRATED …`
  строки при rate).
- `src/DBAL/Types/TzDateStringType.php` + `config/packages/doctrine.yaml`
  — локальный DBAL-тип для timestamptz → строка UTC `Y-m-d H:i:sO` (ключи
  сущности `Price.validFrom` — часть составного PK, поэтому скаляр).
- `migrations/Version20261027000001.php` — CREATE TABLE/INDEX + down-DROP
  (одна миграция; `up(Schema $schema)` и `$this->addSql` по API установленного
  `doctrine/migrations`).
- `tests/Domain/Billing/{MoneyMath,OffPeak,PricingCalculator}Test.php` —
  юнит-тесты домена (10 тестов, 28 ассерций, `vendor/bin/phpunit`).

## Отступления от каркаса и допущения

1. **Форма «unrated» — строка в stderr.** TASK.md: «Для каждого
   unrated-события этого запуска — строка в stderr:
   `UNRATED <provider>/<provider_request_id> <ts> no_price`». Реализовано
   в `BillingRateCommand` (stdout — ровно `RATED=<n> UNRATED=<m>`).
2. **Схема БД через одну миграцию** (`make migrate` →
   `doctrine:migrations:migrate -n`). На чистой базе всё разворачивается
   в нужном порядке; `down()` — полное удаление таблиц. Это «на ваше
   усмотрение» пункт — выбран migrations, а не `orm:schema:create`, т.к.
   так явно и воспроизводимо.
3. **Хранение времени** — в сущностях время строки цены (`valid_from`)
   и события (`ts`) маппится как строка `Y-m-d H:i:sO` (UTC, смещение
   `+0000`) через локальный DBAL-тип. Прич **в базе** — `timestamptz`
   (нормальная БД-колонка); в **PHP/ключе** — строка, т.к. составной PK
   `Price` = (provider, model, valid_from). Сравнения в DQL идут как
   строки UTC — лексикографический порядок эквивалентен хронологическому,
   поэтому «наибольшее valid_from ≤ ts» корректно. Дедуп по
   `(provider, provider_request_id)` — уникальный индекс; `cost_usd`
   хранится строкой `VARCHAR(30)`, `price_valid_from` — `timestamptz`.
   (Это допущение, не наложенное TASK.md; обоснование — скалярный ключ
   ORM.)
4. **Оф-пик** — окно `[window_start, window_end)` UTC, «включительно/
   исключительно» точно по TASK.md; переход через полночь поддерживается
   (условие `ts ≥ start || ts < end`, если `start > end`). Если
   `start == end` — трактуем как «окно не существует» (вернём `false`);
   альтернативный смысл «круглые сутки» — не оговорён в TASK, выбран
   консервативный вариант.
5. **Суммы отчёта** — арифметика bcmath над уже округлёнными строками
   (scale 16), **без повторного округления** слагаемых; итог и колонка
   `cost_usd` отчёта форматуются ровно 6 знаков после точки (
   `MoneyMath::formatTo6`).
6. **Ретро-коррекция цен (бонус)** — реализована как `app:billing:reprice`:
   перезаписывает строку цен по `(provider, model, valid_from)` и
   пересчитывает те события, которые были расценены **именно под эту**
   строку (по `price_valid_from`), фиксируя дельту в `reprice_log`
   (id, provider, provider_request_id, old_cost, new_cost, delta, applied_at).
   Идемпотентность: повторный вызов с той же строкой не двигает события
   (old == new → skip), строки журнала — только для реально изменённых.
7. **Типы данных БД** — деньги/ставки: `VARCHAR` (цифры + знак, строка
   десятичная); токены: `BIGINT`/`INT`; время: `timestamptz`. Это выбрано
   для «float запрещён» (бд-уровень) и точности сравнений.
8. **Exit-коды** — все 5 команд возвращают 0 при успехе (включая
   «всё уже загружено») и 1 при ошибке (не файл, не JSON, не JSONL,
   некорректные значения, нет таблицы/БД). В rate: unrated — не ошибка,
   только счётчик в stdout + строки в stderr.
9. **Юнит-тесты** — покрыты: `MoneyMath` (roundHalfUpTo6 HALF_UP на
   примерах из TASK, `formatTo6` — ровно 6 знаков, точность сумм),
   `OffPeak` (включ/исключ границ окна, переход через полночь),
   `PricingCalculator` (per_1m по видам, unified, оф-пик как множитель,
   неизвестный unit → исключение). Не покрыты: сервисы/команды (интеграция
   с БД) — по TASK тесты «плюс, но не обязательны».
10. **`app:billing:report` без `--from`/`--to`** — покрывает все события
    (обратная/прямая бесконечность). Сортировка групп — по `user`, затем
    по `model` лексикографически, по возрастанию (TASK.md).
11. **Валидация** — `tokens_*` целые ≥ 0 (JSON-числа без дробной);
    `off_peak` — объект `{window_start, window_end, multiplier}` или `null`;
    `rates` — десятичные строки ≤ 6 знаков (рег. выражение, не `float`);
    `unit` строго один из двух. Некорректная строка JSONL — ошибка,
    код 1 (TASK.md).

## Как проверить (локально, по README/Makefile)

```bash
make db-up            # поднимает PostgreSQL 17 (docker-compose)
make migrate          # CREATE БД (если нет) + накатывает миграцию
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
```

Ожидаемый вывод команды `report` (см. также «Пример расчёта» в TASK.md):

```
user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
TOTAL,,6200000,2300000,2500000,83333,33.662499
```

Тесты домена:

```bash
vendor/bin/phpunit --colors=never
```

## Добавленные зависимости

Новых runtime-зависимостей не потребовалось — используется только то,
что уже в `composer.json`: `symfony/console`, `symfony/framework-bundle`,
`symfony/dotenv`, `symfony/runtime`, `symfony/var-exporter`,
`symfony/yaml`, `doctrine/doctrine-bundle`,
`doctrine/doctrine-migrations-bundle`, `doctrine/orm`, `ext-bcmath`,
`ext-ctype`, `ext-iconv`. В `require-dev` — `phpunit/phpunit` (использован),
`squizlabs/php_codesniffer` и `vimeo/psalm` (не настраивались отдельно,
используются как есть).
