# Учёт расходов на LLM-токены

Реализация тестового задания: консольное приложение на Symfony +
PostgreSQL 17 для приёма справочника цен, журнала событий использования
и расчёта/отчёта расходов в USD.

## Что сделано

### Слои-классы

- **Домен** (`src/Domain/Billing`):
  - `Money` — точная десятичная арифметика на bcmath (масштаб 18),
    `add`/`subtract`/`multiplyBy`/`divideBy`, `halfUp6` — единственное
    допустимое округление события (до 6 знаков, HALF_UP), `formatMicros`
    — строка ровно с шестью знаками после точки (вывод в отчёты).
    Запрет на float в доменных операциях.
  - `OffPeakWindow` — проверка попадания момента (минуты суток UTC) в
    окно `[start, end)`; окно может переходить через полночь;
    множитель — строка bcmath.
  - `PricingScheme` — enum `per_1m_tokens` / `per_1m_tokens_unified`
    (оба за 1 000 000 токенов).
- **Сущности** (`src/Billing/Entity`): `Provider` (off-peak),
  `Model`, `PriceRow` (ценовая строка, `rates` как JSON,
    `validFrom` как момент действия), `UsageEvent` (журнал
    использований; дедупликация по (provider, request_id)),
  `RatedEvent` (результат тарификации: cost_usd, repriced_at),
  `RepriceLog` (журнал корректировок с `affected_units`, old/new сумма,
    дельта).
  Natural key через `\x01`-разделитель
  (например `prism\x01e1` — event,
   `prism\x01lumen-8\x012026-08-01T00:00:00Z` — price row).
- **Storage** (`src/Billing/Store`): `Store` — агрегированные
  операции (upsert, find, «последняя цена для события (max valid_from ≤ ts)»,
  unrated-события, rated-события в период, rated-события по price-row,
  flush/persist).
- **Расчёт** (`src/Billing/Engine`):
  - `EventCostCalculator` — сумма по видам для per-token, общая
    величина для unified; применение off-peak множителя провайдера
    (если время суток события в окне), итог — `roundToMicros()`.
  - `RateService` — `rateAll()`: все unrated-события, выбор ценовой
    строки, расчёт, сохранение RatedEvent + `isRated=true`; unrated-list
    возвращается отдельным массивом (для stderr).
  - `RepriceService` (бонус) — `repricePriceRows(array $rowIds)`:
    находит rated-события, тарифицированные указанными price-rows,
    пересчитывает cost по обновлённым rates, обновляет `repricedAt`,
    пишет RepriceLog с affected_units, old/new суммами и дельтой.
- **Парсеры** (`src/Billing/Parser`):
  - `PricesCatalogLoader` — JSON-справочник:
    upsert провайдеров/моделей/price rows; валидация
    rate-строк по unit.
  - `UsageLoader` — JSONL-файл событий; валидация полей;
    upsert по (provider, request_id).
- **Консольные команды** (`src/Command`):
  - `app:prices:import <file>` — `[OK] Импортирован справочник
    (providers=<n> models=<n> prices=<n>).`
  - `app:usage:import <file>` — `[OK] Импортировано событий:
    вставлено=<n>, дублей=<n>, некорр.<n>.`
    (код 0, даже если все строки — дубли).
  - `app:billing:rate` — stdout: `RATED=<n> UNRATED=<m>`;
    stderr: по одной строке на unrated.
  - `app:billing:report --from=<iso> --to=<iso>` — CSV в stdout:
    заголовок + строки `(user, model)` сортированные по user,
    model; `TOTAL` в конце. `--from` включительно, `--to`
    исключительно, применяется к `ts` событий. unrated-события
    в отчёт не попадают.
  - `app:billing:reprice <file>` (бонус) — upsert ценовых строк
    из файла коррекции, пересчёт rated-событий,
    журнал (RepriceLog); stdout ровно две строки:
    `REPRICED_EVENTS=<n>` и `REPRICE_DELTA_USD=<x.xxxxxx>`.

### Схема БД

`migrations/Version20261027141601.php` — единая миграция, создаёт
все таблицы: `providers`, `models`, `price_rows`, `usage_events`,
`rated_events`, `reprice_logs` (индексы и уникальные ограничения
на (provider_code, request_id) / price row / rated event).

### Команда управления схемой (по умолчанию)

`bin/console doctrine:migrations:migrate`
(или `make migrate`, если `make` настроен в Makefile — см. ниже).

## Отступления от каркаса

1. **Файлы добавлены.**
   - `src/Domain/Billing/Money.php` — новая доменная сущность
     (bcmath-арифметика + round-to-6 HALF_UP).
       Обоснование: задача прямо требует «float запрещено», единый
       класс даёт точность и контролирует единственное
       допустимое округление.
   - `src/Domain/Billing/OffPeakWindow.php` — новая доменная сущность
     (окно времени суток, проверка вхождения, поддержка окна через
     полночь, множитель).
       Обоснование: off-peak описан как доменное правило, а не
       техническая деталь «скрипта».
   - `src/Domain/Billing/PricingScheme.php` — новая доменная
     сущность (enum схем тарификации).
       Обоснование: в задаче две разные схемы — разное поле rates,
       разные формулы; enum + switch в калькуляторе читаемее
       стринг-сравнений.
   - `src/Billing/Entity/{Provider, Model, PriceRow, UsageEvent,
     RatedEvent, RepriceLog, EntityId}.php` — сущности + natural key
     helpers.
   - `src/Billing/Store/Store.php` — единый слой доступа (Doctrine EM
     + DQL). Единый слой — проще, чем 5 Repository'ей, и задачи
     здесь ограничены.
   - `src/Billing/Engine/{EventCostCalculator, RateService,
     RepriceService}.php` — расчёт.
   - `src/Billing/Parser/{PricesCatalogLoader, UsageLoader}.php` —
     парсеры входных форматов в домены.
   - `src/Command/{PricesImportCommand, UsageImportCommand,
     BillingRateCommand, BillingReportCommand, BillingRepriceCommand}.php`
     — консольные команды.
   - `src/Billing/Dto/TokenCounts.php` — Value Object токенов
     (input/output/cache_read/cache_write).
   - `migrations/Version20261027141601.php` — новая миграция схемы.
   - `fixtures/correction.json` — пример файла коррекции для
     `app:billing:reprice`.
   - `COMMIT_MESSAGE.md`, `PR_DESCRIPTION.md` — обязательные файлы.

2. **Новые/изменённые зависимости:** нет. Зависимости уже в
   `vendor/` (Symphony 7.4.21, Doctrine ORM 3, PostgreSQL 17 через
   `docker-compose.yml`).

3. **Альтернативные решения:**
   - Хранение **money** как строки (decimal-18), не как число.
     Обоснование: в задаче прямо запрещён float; строка — каноническая
     форма bcmath-числа, при этом не теряется точность (нет «потерянных
     хвостовых нулей», нет «лишних» хвостовых нулей).
   - `rates` в `price_rows` — JSON-колонка со строковыми значениями.
     Обоснование: unit-зависимая структура (input/output/…/any) не
     раскладывается в отдельные колонки без потери гибкости.
     Валидация ключей — в парсере/калькуляторе.
   - Оф-пик на уровне **провайдера** (не модели, не price row) —
     как в задаче.
   - `user` сохранён со значением usage-события в `rated_events` — это
     упрощает отчёт (без join на usage_events).
   - Reprice: перезапись cost_usd по затронутым строкам + отдельная строка
     в reprice_logs. Обоснование: итоговый `app:billing:report`
     читает rated_events → отражает актуальную сумму; журнал даёт
     «аудит-хвост» (кто, когда, какую сумму, по каким unit-ам
     поменял) без дублирования.
   - Natural key (provider + \x01 + request_id) — в задаче ключ
     «со стороны провайдера»: одинаковые provider_request_id у разных
     провайдеров — разные события, поэтому id строится из обоих.
   - Отчёт `app:billing:report`: `model` — `<provider>/<code>`
     (строит на лету через модель).
   - `app:billing:rate` idempotent: повторный запуск — `RATED=0
     UNRATED=0`, так как все события уже оценены.
   - `app:usage:import` idempotent: повторный импорт тех же событий —
     0 вставлено, n дублей.
   - `app:prices:import` idempotent: upsert по (provider, model,
     valid_from).

## Как проверить

```bash
# 1. Чистая БД, развёрнутая схема (схема — миграция)
make db-down && make db-up
docker exec repo-db-1 psql -U app -d app -c 'DROP SCHEMA public CASCADE; CREATE SCHEMA public;'
bin/console doctrine:migrations:migrate

# 2. Импорт справочника + событий + тарификация
bin/console app:prices:import fixtures/prices.json
bin/console app:usage:import fixtures/usage.jsonl
bin/console app:billing:rate  # -> RATED=3 UNRATED=0

# 3. Отчёт сентября (байт-в-байт по TASK.md)
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
#   user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
#   u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
#   u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
#   u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
#   TOTAL,,6200000,2300000,2500000,83333,33.662499

# 4. Идемпотентность: повторный импорт + rate + report — результат не меняется
bin/console app:prices:import fixtures/prices.json   # -> OK
bin/console app:usage:import fixtures/usage.jsonl    # -> 0 вставлено, 3 дублей
bin/console app:billing:rate                          # -> RATED=0 UNRATED=0
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z

# 5. Бонус: ретро-коррекция цены
bin/console app:billing:reprice fixtures/correction.json
#   REPRICED_EVENTS=1
#   REPRICE_DELTA_USD=-2.387500
bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
#   u-alice,prism/lumen-8,...,11.874999   (пересчитано)
```

## Допущения

1. **Без теста-файлов** (PHPUnit есть в vendor, но не обязательны).
   Точность проверена «вручную» на примерах из TASK.md.
2. **Оф-пик на уровне провайдера** — как в TASK.md.
3. **Reprice-механизм** — перезапись rated_events + отдельная строка
   в reprice_logs (аудит).
4. **Natural key** строится через `\x01` — уникальный внутри БД,
   не зависит от длины кодов; декодер `EntityId::event()`, `::priceRow()`.
5. **Округление** — 1 раз на событие (в калькуляторе), суммы —
   без повторного округления (как в TASK.md 4).
6. **Unrated-события** — не считается ошибкой, при повторном тарифе
   пробуется ещё раз (на случай, что цена «догнала»).
